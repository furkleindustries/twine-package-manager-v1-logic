<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class LoginDeleteEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $loggedInUser = $container->get("loggedInUser");
        $container->get("unpersistLoginSession")($loggedInUser);

        $successArray = $container->get("successArray");
        $successArray["userId"] = $userId;
        $successStr = json_encode($successArray);
        $body = $container->get("responseBody");
        $body->write($successStr);
        $response = $container->get("response")->withBody($body);
        $response->userId = $userId;
        return $response;
    }
}