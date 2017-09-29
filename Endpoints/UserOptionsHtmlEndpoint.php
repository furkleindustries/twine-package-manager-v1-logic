<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class ServerUserOptionsGetEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $currentUser = $container->get("loggedInUser");
        $templatingAuths = [];
        if ($currentUser) {
            /* Throws exception if invalid. */
            $id = $container->get("filterId")($currentUser["id"]);
            $sqlAbstractionType = "authorization";
            $getFromUserId = $container->get("getAbstractionFromUserId");
            $authorizations = $getFromUserId($sqlAbstractionType, $id);
            $key = "transformAuthorizationToTemplatingArray";
            $transformer = $container->get($key);
            $templatingAuths = $transformer($auths);
        }

        $response = $container->get("response");
        $response->templateVars = [
            "loggedInUser" => $currentUser,
            "authorizations" => $templatingAuths,
        ];

        return $response;
    }
}