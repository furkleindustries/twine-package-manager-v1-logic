<?php
namespace TwinePM\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
class NoIFramesMiddleware implements IMiddleware {
    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        $noIframesResponse = $response->withHeader("X-Frame-Options", "DENY");
        return $next($request, $noIframesResponse);
    }
}