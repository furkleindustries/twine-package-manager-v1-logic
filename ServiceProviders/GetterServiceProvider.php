<?php
namespace TwinePM\ServiceProviders;

use Slim\ContainerInterface;
use Slim\DefaultServicesProvider;
use TwinePM\Getters\AuthorizedUserGetter;
use TwinePM\Getters\AuthorizationTokenGetter;
use TwinePM\Getters\AuthorizationServerGetter;
use TwinePM\Getters\CacheClientGetter;
use TwinePM\Getters\CacheServerUrlGetter;
use TwinePM\Getters\DatabaseClientGetter;
use TwinePM\Getters\DatabaseClientArgsGetter;
use TwinePM\Getters\DatabaseServerUrlGetter;
use TwinePM\Getters\DsnGetter;
use TwinePM\Getters\FileContentsGetter;
use TwinePM\Getters\KeyGetter;
use TwinePM\Getters\RequestIdGetter;
use TwinePM\Getters\SaltGetter;
use TwinePM\Getters\ServerDomainNameGetter;

class GetterServiceProvider extends DefaultServicesProvider {
    function register(ContainerInterface $container) {
        $container["authorizationToken"] = function () {
            $authorizationTokenGetter = $this->get("authorizationTokenGetter");
            return $authorizationTokenGetter($this->get("request"));
        };

        $container["authorizationTokenGetter"] = function () {
            return new AuthorizationTokenGetter();
        };

        $container["authorizationServer"] = function () {
            return $this->get("authorizationServerGetter")();
        };

        $container["authorizationServerGetter"] = function () {
            return new AuthorizationServerGetter();
        };

        $container["authorizedUser"] = function () {
            $token = $this->get("authorizationToken");
            $db = $this->get("diskDatabaseClient");
            return $this->get("authorizedUserGetter")($token, $db);
        };

        $container["authorizedUserGetter"] = function () {
            return new AuthorizedUserGetter();
        };

        $container["diskDatabaseClient"] = function () {
            $dsn = $this->get("dsn");
            $dbArgs = $this->get("diskDatabaseClientArgs");
            $username = $dbArgs["user"];
            $password = $dbArgs["pass"];
            $diskDatabaseClientGetter = $this->get("diskDatabaseClientGetter");
            return $diskDatabaseClientGetter($dsn, $username, $password);
        };

        $container["diskDatabaseClientGetter"] = function () {
            return new DiskDatabaseClientGetter();
        };

        $container["diskDatabaseClientArgs"] = function () {
            $key = "diskDatabaseClientArgsGetter";
            $diskDatabaseClientArgsGetter = $this->get($key);
            $diskDatabaseServerUrl = $this->get("diskDatabaseServerUrl");
            return $diskDatabaseClientArgsGetter($diskDatabaseServerUrl);
        };

        $container["diskDatabaseClientArgsGetter"] = function () {
            return new DiskDatabaseClientArgsGetter();
        };

        $key = "databaseClientWithExceptions";
        $container[$key] = $container->factory(function () {
            $client = $this->get("databaseClient");
            $errmodeKey = $client::ATTR_ERRMODE;
            $errmodeValue = $client::ERRMODE_EXCEPTION;
            $client->setAttribute($errmodeKey, $errmodeValue);
            return $client;
        });

        $container["databaseServerDsn"] = function () {
            $driver = "pgsql";
            $dbName = ltrim($dbArgs["path"], "/");
            $charset = "utf8";
            return $this->get("makeDsn")(
                $driver,
                $dbArgs["host"],
                $dbArgs["port"],
                $dbName,
                $charset);
        };

        $container["databaseServerUrlGetter"] = function () {
            return new DatabaseServerUrlGetter();
        };

        $container["databaseServerUrl"] = function () {
            return $this->get("databaseServerUrlGetter")();
        };

        $container["dsnGetter"] = function () {
            return new DsnGetter();
        };

        $container["dsn"] = function () {
            return $this->get("dsnGetter")($this->get("databaseClientArgs"));
        };

        $container["fileContentsGetter"] = function () {
            return new FileContentsGetter();
        };

        $container["key"] = function () {
            return $this->get("keyGetter")(
                $this->get("loadKeyFromString"),
                $this->get("fileContentsGetter"),
                $this->get("fileContentsPersister"),
                $this->get("generateKey"));
        };

        $container["keyGetter"] = function () {
            return new KeyGetter();
        };

        $container["loggedInUserGetter"] = function () {
            return new LoggedInUserGetter();
        };

        $container["loggedInUser"] = function () {
            $request = $this->get("request");
            $cache = $this->get("cacheClient");
            return $this->get("loggedInUserGetter")($request, $cache);
        });

        $container["memoryDatabaseClient"] = function () {
            $cacheClientGetter = $this->get("cacheClientGetter");
            return $cacheClientGetter($this->get("cacheServerUrl"));
        };

        $container["memoryDatabaseClientGetter"] = function () {
            return new MemoryDatabaseClientGetter();
        };

        $container["memoryDatabaseServerUrl"] = function () {
            return $this->get("memoryDatabaseServerUrlGetter")();
        };

        $container["memoryDatabaseServerUrlGetter"] = function () {
            return new MemoryDatabaseServerUrlGetter();
        };

        $container["requestId"] = function () {
            return $this->get("requestIdGetter")($this->get("key"));
        };

        $container["requestIdGetter"] = function () {
            return new RequestIdGetter();
        };

        $container["salt"] = $container->factory(function () {
            return $this->get("saltGetter")($this->get("key"));
        });

        $container["saltGetter"] = function () {
            return new SaltGetter();
        };

        $container["serverDomainName"] = function () {
            return $this->get("serverDomainNameGetter")();
        };

        $container["serverDomainNameGetter"] = function () {
            return new ServerDomainNameGetter();
        };

        $container["templater"] = function () {
            return $this->get("templaterGetter")();
        };

        $container["templaterGetter"] = function () {
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