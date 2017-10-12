<?php
namespace TwinePM\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class DependencyContainerMiddleware implements IMiddleware {
    private $container;

    function __construct(Container $container) {
        $this->container = $container;
    }

    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        $settings = [
            "displayErrorDetails" => true,
        ];

        $containerGetter = new DependencyContainerGetter();
        $container = $containerGetter($req, $res, $settings);
        try {
            return $next($req, $res);
        } catch (ITwinePmException $e) {
            $errorCode = $e->getErrorCode();
            $res = $response->withHeader("X-TwinePM-Error-Code", $errorCode);
            $this->container->get("loggerRouter")->route($e);
            return $res;
        } catch (Exception $e) {
            /* TODO: Add real error handling. */
            die("Unknown error.");
        }
    }
}