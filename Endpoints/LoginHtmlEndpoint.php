<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class LoginHtmlEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $response = $container->get("response");
        $response->templateVars = [
            "loggedInUser" => $container->get("loggedInUser"),
        ];

        return $response;
    }
}