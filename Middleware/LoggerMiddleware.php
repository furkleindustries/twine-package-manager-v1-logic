<?php
namespace TwinePM\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
class LoggerMiddleware implements IMiddleware {
    private $logger;

    function __construct(AbstractLogger $logger) {
        $this->logger = $logger;
    }

    function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next): ResponseInterface
    {
        $accessLogger = new AccessLogger();

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

        $accessLogger->log($logArray);

        return $next($request, $response);
    }
}