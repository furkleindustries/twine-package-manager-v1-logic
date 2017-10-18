<?php
namespace TwinePM\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use TwinePM\Loggers\ILogger;
class AccessLoggerMiddleware implements IMiddleware {
    private $logger;

    function __construct(ILogger $logger) {
        $this->logger = $logger;
    }

    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        $bodyParams = $request->getParsedBody() ?? [];
        $logArray = [
            "query" => $request->getQueryParams(),
            "body" => $bodyParams,
            "headers" => $request->getHeaders(),
            "server" => $request->getServerParams(),
        ];

        unset($logArray["query"]["password"]);
        unset($logArray["body"]["password"]);
        unset($logArray["server"]["SERVER_SOFTWARE"]);
        unset($logArray["server"]["SCRIPT_NAME"]);
        unset($logArray["server"]["DOCUMENT_ROOT"]);

        /* Deduplicate headers from server. */
        foreach ($logArray["server"] as $key => $value) {
            if (array_key_exists($key, $logArray["headers"])) {
                unset($logArray["server"][$key]);
            }
        }

        $this->logger->log($logArray);

        return $next($request, $response);
    }
}