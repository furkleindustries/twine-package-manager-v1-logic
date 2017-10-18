<?php
namespace TwinePM\ServiceProviders;

use Defuse\Crypto;
use Defuse\Crypto\Key;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use TwinePM\ServiceProviders\EndpointServiceProvider;
use TwinePM\ServiceProviders\FilterServiceProvider;
use TwinePM\ServiceProviders\GetterServiceProvider;
use TwinePM\ServiceProviders\LoggerServiceProvider;
use TwinePM\ServiceProviders\PersisterServiceProvider;
use TwinePM\ServiceProviders\SearchServiceProvider;
use TwinePM\ServiceProviders\SorterServiceProvider;
use TwinePM\ServiceProviders\SqlAbstractionServiceProvider;
use TwinePM\ServiceProviders\TransformerServiceProvider;
use TwinePM\ServiceProviders\ValidatorServiceProvider;
class MiscellaneousServiceProvider implements ServiceProviderInterface {
    function register(Container $container) {

        $container["environmentMode"] = function () {
            $env = getenv("TWINEPM_MODE");
            return $env ? $env : "production";
        };

        $func = function (string $message) {
            $algo = "sha256";
            $key = $this->get("key");
            $keyStr = $key->saveToAsciiSafeString();
            return hash_hmac($algo, $message, $keyStr);
        };

        $container["generateDigest"] = $container->protect($func);

        $container["generateKey"] = $container->protect(function () {
            return Key::createNewRandomKey();
        });

        $container["makeDsn"] = $container->protect(function (
            string $driver,
            string $host,
            string $port,
            string $dbName,
            string $charset): string
        {
            if ($driver !== "pgsql" and $charset) {
                return sprintf("%s:host=%s;port=%s;dbname=%s;charset=%s;",
                    $driver,
                    $host,
                    $port,
                    $dbName,
                    $charset);
            } else {
                return sprintf("%s:host=%s;port=%s;dbname=%s;",
                    $driver,
                    $host,
                    $port,
                    $dbName);
            }
        });
        
        $container["successObject"] = [
            "status" => 200,
        ];

        $container["templater"] = function () {
            $view = new Twig(__DIR__ . "/templates/", [
                "cache" => "templates/compilation_cache/",
            ]);
            
            /* Instantiate and add Slim specific extension. */
            $untrimmed = str_ireplace(
                "index.php",
                "",
                $container["request"]->getUri()->getBasePath());

            $basePath = rtrim($untrimmed, "/");
            $router = $container->get("router");
            $view->addExtension(new TwigExtension($router, $basePath));
            return $view;
        };
    }
}