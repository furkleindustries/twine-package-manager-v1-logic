<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class LogoutPostEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $loggedInUser = $container->get("loggedInUser");
        $container->get("unpersistLoginSession")($loggedInUser);

        $body = $container->get("responseBody");
        $successArray = $container->get("successArray");
        $successArray["userId"] = $userId;
        $successStr = json_encode($successArray);
        $body->write($successStr);
        $response = $container->get("response")->withBody($body);
        $response->userId = $userId;
        return $response;
    }
}