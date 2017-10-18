<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use TwinePM\Exceptions\UserRequestFieldInvalidException;
class AuthorizationDeleteEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $request = $container->get("request");
        $source = $request->getParsedBody();
        $authorization = null;
        $sqlAbstractionType = "authorization";
        if (array_key_exists("globalAuthorizationId", $source)) {
            $globalAuthorizationId = $source["globalAuthorizationId"];
            /* Throws exception if invalid. */
            $id = $container->get("idFilter")($globalAuthorizationId);
            $key = "getAbstractionFromPrimaryKey";
            $getFromPrimaryKey = $container->get($key);
            $authorization = $getFromPrimaryKey($sqlAbstractionType, $id);
        } else if (isset($source["oAuthToken"])) {
            $getFromToken = $container->get("getAbstractionFromToken");
            $authorization = $getFromToken($source["oAuthToken"]);
        } else {
            $errorCode = "IdentifierInvalid";
            throw new UserRequestFieldInvalidException($errorCode);
        }

        $authorization->deleteFromDatabase();
    }
}