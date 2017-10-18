<?php
namespace TwinePM\Middleware;

use ArrayAccess;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
class ServiceProviderMiddleware implements IMiddleware {
    private $container;
    private $serviceProviders;

    function __construct(Container $container, ArrayAccess $serviceProviders) {
        $this->container = $container;
        $this->serviceProviders = $serviceProviders;
    }

    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        foreach ($this->serviceProviders as $serviceProvider) {
            $this->container->register($serviceProvider);
        }

        try {
            return $next($req, $res);
        } catch (ITwinePmException $e) {
            $errorCode = $e->getErrorCode();
            $res = $response->withHeader("X-TwinePM-Error-Code", $errorCode);
            return $this->container->get("loggerRouter")->route($e);
        } catch (Exception $e) {
            /* TODO: Add real error handling. */
            die("Unknown error.");
        }
    }
}