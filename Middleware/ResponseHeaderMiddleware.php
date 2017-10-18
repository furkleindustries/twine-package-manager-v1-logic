<?php
namespace TwinePM\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
class ResponseHeaderMiddleware implements IMiddleware {
    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        $sts = "max-age=31536000; includeSubDomains";
        $headeredResponse = $response
            ->withoutHeader("Server")
            ->withoutHeader("X-Powered-By")
            ->withHeader("Strict-Transport-Security", $sts)
            ->withHeader("X-Content-Type-Options", "nosniff")
            ->withHeader("X-Frame-Options", "DENY")
            ->withHeader("X-XSS-Protection", "1; mode=block");

        return $next($request, $headeredResponse);
    }
}