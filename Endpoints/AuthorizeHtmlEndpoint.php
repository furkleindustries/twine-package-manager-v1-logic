<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use TwinePM\Exceptions\InvalidArgumentException;
use TwinePM\Exceptions\NoResultExistsException;
use TwinePM\Exceptions\PersistenceFailedException;
class AuthorizeHtmlEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $source = $request->getQueryParams();
        if (!isset($source["state"]) or !$source["state"]) {
            $errorCode = "AuthorizationStateMissing";
            throw new InvalidArgumentException($errorCode);
        }

        $responseType = isset($source["response_type"]) ?
            $source["response_type"] : null;
        if ($responseType !== "token") {
            $errorCode = "AuthorizationResponseTypeInvalid";
            throw new InvalidArgumentException($errorCode);
        }

        $clientId = isset($source["client_id"]) ? $source["client_id"] : null;
        if (!$clientId) {
            $errorCode = "AuthorizationClientIdInvalid";
            throw new InvalidArgumentException($errorCode);
        }

        $authServer = $container->get("authorizationServer");

        /* Throws exception if invalid. */
        $authRequest = $authServer->validateAuthorizationRequest($request);

        $value = [
            "requestId" => $requestId,
            "authorizationRequest" => $authRequest,
        ];

        $key = "persistAuthorizationRequest";
        $persistAuthorizationRequest = $container->get($key);
        $domain = $container->get("serverDomainName");
        $persistAuthorizationRequest($domain, $authRequest);

        $clientRepo = $container->get("clientRepository");
        $clients = $clientRepo->getClients();
        $client = isset($clients[$clientId]) ? $clients[$clientId] : null;
        if (!$client) {
            $errorCode = "AuthorizationHtmlClientLookup";
            throw new ClientDoesNotExistException($errorCode);
        }

        $scopeObjects = $container->get("scopeRepository")::SCOPES;
        $scopes = explode(" ", $source["scopes"]);
        $scopes = array_map(function($a) {
            return $scopeObjects[$a];
        }, $scopes);

        $response = $container->get("response");
        $response->templateVars = [
            "client" => $client,
            "scopes" => $scopes,
            "loggedInUser" => $container->get("loggedInUser"),
        ];

        return $response;
    }
}