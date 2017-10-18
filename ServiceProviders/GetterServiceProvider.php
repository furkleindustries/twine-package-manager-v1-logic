<?php
namespace TwinePM\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TwinePM\Getters\AppSettingsGetter;
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
use TwinePm\Getters\TwinePmEpochGetter;
class GetterServiceProvider implements ServiceProviderInterface {
    function register(Container $container) {
        $appSettings = function () {
            return $this->get("appSettingsGetter")();
        };

        $appSettingsGetter = function () {
            return new AppSettingsGetter();
        };

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
            $builder = function (
                $privateKey,
                $encryptionKey,
                $accessTokenRepository
                $clientRepository,
                $scopeRepository)
            {
                return new AuthorizationServer(
                    $clientRepository,
                    $accessTokenRepository,
                    $scopeRepository,
                    $privateKey,
                    $encryptionKey);
            };

            $tokenLifeInterval = new DateInterval("P30D");
            return new AuthorizationServerGetter(
                $this->get("oAuthCryptKey"),
                $this->get("oAuthEncryptionKey"),
                $tokenLifeInterval,
                $this->get("accessTokenRepository"),
                $this->get("clientRepository"),
                $this->get("scopeRepository"),
                $this->get("implicitGrant"),
                $this->get("filePathToFileContentsTransformer"),
                $builder);
        };

        $container["authorizedUser"] = function () {
            $token = $this->get("authorizationToken");
            $db = $this->get("diskDatabaseClient");
            return $this->get("authorizedUserGetter")($token, $db);
        };

        $container["authorizedUserGetter"] = function () {
            return new AuthorizedUserGetter();
        };

        $container["cryptKey"] = function () {
            return $this->get("cryptKeyGetter")();
        };

        $container["cryptKeyGetter"] = function () {
            $filePath = __DIR__ . "/../crypto/oAuthPrivate.key";
            $builder = function ($filePath) {
                return new CryptKey($filePath);
            };

            return new CryptKeyGetter($filePath, $builder);
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
            return $this->get("diskDatabaseClientArgsGetter")();
        };

        $container["diskDatabaseClientArgsGetter"] = function () {
            $serverUrl = $this->get("diskDatabaseServerUrl");
            return new DiskDatabaseClientArgsGetter($serverUrl);
        };

        $key = "diskDatabaseClientWithExceptions";
        $container[$key] = $container->factory(function () {
            $client = $this->get("diskDatabaseClient");
            $errmodeKey = $client::ATTR_ERRMODE;
            $errmodeValue = $client::ERRMODE_EXCEPTION;
            $client->setAttribute($errmodeKey, $errmodeValue);
            return $client;
        });

        $container["diskDatabaseServerDsn"] = function () {
            $driver = "pgsql";
            $dbArgs = $this->get("diskDatabaseClientArgs");
            $dbName = ltrim($dbArgs["path"], "/");
            $charset = "utf8";
            return $this->get("makeDsn")(
                $driver,
                $dbArgs["host"],
                $dbArgs["port"],
                $dbName,
                $charset);
        };

        $container["diskDatabaseServerUrlGetter"] = function () {
            return new DiskDatabaseServerUrlGetter();
        };

        $container["diskDatabaseServerUrl"] = function () {
            return $this->get("diskDatabaseServerUrlGetter")();
        };

        $container["dsnGetter"] = function () {
            return new DsnGetter($this->get("diskDatabaseClientArgs"));
        };

        $container["dsn"] = function () {
            return $this->get("dsnGetter")();
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

        $container["loggedInUser"] = function () {
            $request = $this->get("request");
            $cache = $this->get("cacheClient");
            return $this->get("loggedInUserGetter")($request, $cache);
        };

        $container["loggedInUserGetter"] = function () {
            return new LoggedInUserGetter();
        };

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
            return $this->get("requestIdGetter")();
        };

        $container["requestIdGetter"] = function () {
            return new RequestIdGetter($this->get("key"));
        };

        $container["salt"] = $container->factory(function () {
            return $this->get("saltGetter")();
        });

        $container["saltGetter"] = function () {
            return new SaltGetter($this->get("key"));
        };

        $container["serverDomainName"] = function () {
            return $this->get("serverDomainNameGetter")();
        };

        $container["serverDomainNameGetter"] = function () {
            return new ServerDomainNameGetter();
        };

        $container["twinePmEpoch"] = function () {
            return $this->get("twinePmEpochGetter")();
        };

        $container["twinePmEpochGetter"] = function () {
            return new TwinePmEpochGetter();
        };
    }
}