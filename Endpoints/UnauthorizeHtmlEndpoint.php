<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class UnauthorizeHtmlEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $user = $container->get("loggedInUser");
        $sqlAbstractionType = "authorization";
        $getFromUserId = $container->get("getAbstractionFromUserId");
        $authorizations = $getFromUserId($user["id"], $db);

        $key = "transformAuthorizationToTemplatingArray";
        $transformer = $container->get($key);
        $templatingAuths = $transform($authorizations);

        $response = $container->get("request");
        $response->templateVars = [
            "authorizations" => $templatingAuths,
            "loggedInUser" => $user,
        ];

        return $response;
    }
}