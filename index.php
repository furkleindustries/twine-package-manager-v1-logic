<?php
declare(strict_types = 1);

namespace TwinePM;

require_once __DIR__ . "/vendor/autoload.php";

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Slim\App;
use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use TwinePM\Endpoints;
use TwinePM\Exceptions\ITwinePmException;
use TwinePM\Getters\AppSettingsGetter;
use TwinePM\Loggers\AccessLogger;
use TwinePM\Middleware\DependencyContainerMiddleware;
use TwinePM\Middleware\ResponseHeaderMiddleware;
use TwinePM\Middleware\AccessLoggerMiddleware;
use TwinePM\OAuth2\Entities\ClientEntity;
use TwinePM\OAuth2\Entities\UserEntity;
use TwinePM\ServiceProviders\EndpointServiceProvider;
use TwinePM\ServiceProviders\FilterServiceProvider;
use TwinePM\ServiceProviders\GetterServiceProvider;
use TwinePM\ServiceProviders\LoggerServiceProvider;
use TwinePM\ServiceProviders\MiscellaneousServiceProvider;
use TwinePM\ServiceProviders\PersisterServiceProvider;
use TwinePM\ServiceProviders\SearchServiceProvider;
use TwinePM\ServiceProviders\SorterServiceProvider;
use TwinePM\ServiceProviders\SqlAbstractionServiceProvider;
use TwinePM\ServiceProviders\TransformerServiceProvider;
use TwinePM\ServiceProviders\ValidatorServiceProvider;

$appSettingsGetter = new AppSettingsGetter();
$initialContainer = [ "settings" => $appSettingsGetter(), ];
$app = new App($initialContainer);

$container = $app->getContainer();
$serviceProviders = [
    new EndpointServiceProvider(),
    new FilterServiceProvider(),
    new GetterServiceProvider(),
    new LoggerServiceProvider(),
    new MiscellaneousServiceProvider(),
    new PersisterServiceProvider(),
    new SearchServiceProvider(),
    new SorterServiceProvider(),
    new SqlAbstractionServiceProvider(),
    new TransformerServiceProvider(),
    new ValidatorServiceProvider(),
];

/* Fires third and last. */
$app->add(new ServiceProviderMiddleware($container, $serviceProviders));

/* Fires second. */
$app->add(new ResponseHeaderMiddleware());

/* Fires first. Must be performed before any probable exceptions. */
$app->add(new AccessLoggerMiddleware(new AccessLogger()));

$rootMethods = [
    "GET",
];

$root = function (Request $request, Response $response) {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $rootMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $rootMethods));

    $rootHtml = $container->get("rootHtmlEndpoint");
    if ($request->isGet()) {
        $res = $rootHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "index.html.twig";
        return $container
            ->get("templater")
            ->render($res, $filepath, $templateVars);
    }
};

$app->map($rootMethods, "[/]", $root);

$createAccountMethods = [
    "GET",
];

$createAccount = function(Request $request, Response $response) {
    $container = $this;

    $container["response"] = $response
        ->withHeader("Allow", implode(",", $versionMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $versionMethods));

    $createAccountHtml = $container->get("createAccountHtmlEndpoint");
    if ($request->isGet()) {
        $res = $createAccountHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "createAccount.html.twig";
        return $container
            ->get("templater")
            ->render($res, $filepath, $templateVars);
    }
};

$app->map($createAccountMethods, "/createAccount[/]", $createAccount);

$loginMethods = [
    "GET",
    "POST",
    "DELETE",
    "OPTIONS",
];

$login = function (Request $request, Response $response): Response {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $versionMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $versionMethods));

    $loginHtml = $container->get("loginHtmlEndpoint");
    $loginCreate = $container->get("loginCreateEndpoint");
    $loginDelete = $container->get("loginDeleteEndpoint");
    if ($request->isGet()) {
        $res = $loginHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "login.html.twig";
        return $this->get("templater")->render($res, $filepath, $templateVars);
    } else if ($request->isPost()) {
        $res = $loginCreate($container);
        $serverUrl = $container->get("serverUrl");
        return $res
            ->withHeader("Access-Control-Allow-Origin", $serverUrl)
            ->withRedirect("/options", 302);
    } else if ($request->isDelete()) {
        $res = $loginDelete($container);
        return $res
            ->withHeader("Access-Control-Allow-Origin", $serverUrl)
            ->withRedirect("/", 302);
    } else if ($request->isOptions()) {
        $options = [
            "POST" => $accountCreate->getOptionsObject(),
            "DELETE" => $accountDelete->getOptionsObject(),
        ];

        return $response->withJson($options);
    }
};

$app->map($loginMethods, "/login[/]", $login);

$logoutMethods = [
    "GET",
];

$logout = function (Request $request, Response $response): Response {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $logoutMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $logoutMethods));

    if ($request->isGet()) {
        $res = $logoutHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "logout.html.twig";
        return $container
            ->get("templater")
            ->render($res, $filepath, $templateVars);
    }
};

$app->map($logoutMethods, "/logout[/]", $logout);

$authorizeMethods = [
    "GET",
];

$authorize = function (Request $request, Response $response): Response {
    $authorizeHtml = $container->get("authorizeHtmlEndpoint");
    if ($request->isGet()) {
        $res = $authorizeHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "authorize.html.twig";
        return $this->get("templater")->render($res, $filepath, $templateVars);
    }
};

$app->map($authorizeMethods, "/authorize[/]", $authorize);

$authorizationMethods = [
    "POST",
    "DELETE",
    "OPTIONS",
];

$authorization = function (Request $request, Response $response): Response {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $authorizationMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $authorizationMethods));
    
    $authorizationCreate = $container->get("authorizationCreateEndpoint");
    $authorizationDelete = $container->get("authorizationDeleteEndpoint");
    if ($request->isPost()) {
        $res = $authorizationCreate($container);
        $server = $this->get("authorizationServer");

        /* Redirects to the target client's authorization endpoint. */
        return $server->completeAuthorizationRequest(
            $res->authRequest,
            $res);
    } else if ($request->isDelete()) {
        return $authorizationDelete($container);
    } else if ($request->isOptions()) {
        $options = [
            "POST" => $authorizationCreate->getOptionsObject(),
            "DELETE" => $authorizationDelete->getOptionsObject(),
        ];

        return $res->withJson($options);
    }
};

$app->map($authorizationMethods, "/authorization[/]", $authorization);

$unauthorizeMethods = [
    "GET",
];

$unauthorize = function (Request $request, Response $response): Response {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $unauthorizeMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $unauthorizeMethods));

    $unauthorizeHtml = $container->get("unauthorizeHtmlEndpoint");
    if ($request->isGet()) {
        $container = $this;
        $res = $unauthorizeHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "unauthorize.html.twig";
        return $this->get("templater")->render($res, $filepath, $templateVars);
    }
};

$app->map($unauthorizeMethods, "/unauthorize[/]", $unauthorize);

$clientsMethods = [
    "GET",
];

$clients = function(Request $request, Response $response) {
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $clientsMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $clientsMethods));

    $clientsHtml = $container->get("clientsHtmlEndpoint");
    if ($request->isGet()) {
        $container = $this;
        $res = $clientsHtml($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "clients.html.twig";
        return $this->get("templater")->render($res, $filepath, $templateVars);
    }
};

$app->map($clientsMethods, "/clients[/]", $clients);

$optionsMethods = [
    "GET",
];

$options = function(
    Request $request,
    Response $response) use ($optionsMethods)
{
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $optionsMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $optionsMethods));

    $serverUserOptions = $container->get("ServerUserOptionsHtmlEndpoint");
    if ($request->isGet()) {
        $res = $serverUserOptions($container);
        $templateVars = isset($res->templateVars) ? $res->templateVars : [];
        $filepath = "options.html.twig";
        return $this->get("templater")->render($res, $filepath, $templateVars);
    }
};

$app->map($optionsMethods, "/options[/]", $options);

$accountMethods = [
    "GET",
    "POST",
    "PUT",
    "DELETE",
    "OPTIONS",
];

$account = function(
    Request $request,
    Response $response)
    use ($accountMethods): Response
{
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $accountMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $accountMethods));

    $accountCreate = $this->get("accountCreationEndpoint");
    $accountRead = $this->get("accountGetEndpoint");
    $accountUpdate = $this->get("accountUpdateEndpoint");
    $accountDelete = $this->get("accountDeleteEndpoint");
    if ($request->isPost()) {
        return $accountCreate($container);
    } else if ($request->isGet()) {
        return $accountRead($container);
    } else if ($request->isPut()) {
        return $accountUpdate($container);
    } else if ($request->isDelete()) {
        return $accountDelete($container);
    } else if ($request->isOptions()) {
        $options = [
            "GET" => $accountRead->getOptionsObject(),
            "POST" => $accountCreate->getOptionsObject(),
            "PUT" => $accountUpdate->getOptionsObject(),
            "DELETE" => $accountDelete->getOptionsObject(),
        ];

        return $res->withJson($options);
    }
};

$app->map($accountMethods, "/account[/]", $account);

$profileMethods = [
    "GET",
    "OPTIONS",
];

$profile = function (
    Request $request,
    Response $response)
    use ($profileMethods): Response
{
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $profileMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $profileMethods));

    $profileRead = $this->get("profileReadEndpoint");
    if ($request->isGet()) {
        return $profileRead($container);
    } else if ($request->isOptions()) {
        $options = [
            "GET" => $profileRead->getOptionsObject(),
        ];

        return $res->withJson($options);
    }
};

$app->map($profileMethods, "/profile[/]", $profile);

$packageMethods = [
    "GET",
    "POST",
    "PUT",
    "DELETE",
    "OPTIONS",
];

$package = function (
    Request $request,
    Response $response)
    use ($packageMethods): Response
{
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $packageMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $packageMethods));

    $packageCreate = $this->get("packageCreateEndpoint");
    $packageRead = $this->get("packageReadEndpoint");
    $packageUpdate = $this->get("packageUpdateEndpoint");
    $packageDelete = $this->get("packageDeleteEndpoint");
    if ($request->isGet()) {
        return $packageRead($container);
    } else if ($request->isPost()) {
        return $packageCreate($container);
    } else if ($request->isPut()) {
        return $packageUpdate($container);
    } else if ($request->isDelete()) {
        return $packageDelete($container);
    } else if ($request->isOptions()) {
        $options = [
            "GET" => $packageRead->getOptionsObject(),
            "POST" => $packageCreate->getOptionsObject(),
            "PUT" => $packageUpdate->getOptionsObject(),
            "DELETE" => $packageDelete->getOptionsObject(),
        ];

        return $res->withJson($options);
    }
};

$app->map($packageMethods, "/package[/]", $package);

$versionMethods = [
    "GET",
    "POST",
    "DELETE",
    "OPTIONS",
];

$version = function (
    Request $request,
    Response $response)
    use ($versionMethods): Response
{
    $container = $this;
    $container["response"] = $response
        ->withHeader("Allow", implode(",", $versionMethods))
        ->withHeader(
            "Access-Control-Allow-Methods",
            implode(",", $versionMethods));

    $versionCreate = $this->get("versionCreateEndpoint");
    $versionRead = $this->get("versionReadEndpoint");
    $versionDelete = $this->get("versionDeleteEndpoint");
    if ($request->isGet()) {
        return $versionRead($container);
    } else if ($request->isPost()) {
        return $versionCreate($container);
    } else if ($request->isDelete()) {
        return $versionDelete($container);
    } else if ($request->isOptions()) {
        $options = [
            "GET" => $versionRead->getOptionsObject(),
            "POST" => $versionCreate->getOptionsObject(),
            "DELETE" => $versionDelete->getOptionsObject(),
        ];

        return $res->withJson($options);
    }
};

$app->map($versionMethods, "/version[/]", $version);

/* Run the app, executing the middleware stack, then the map function, then
 * the Endpoint, etc. Errors are caught and rendered as HTML. */
$app->run();