<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class RootHtmlEndpoint extends AbstractEndpoint {
    function __invoke(Container $container): ResponseInterface {
        $success = $container->get("response");
        $success->templateVars = [];
        return $success;
    }
}