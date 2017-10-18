<?php
namespace TwinePM\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TwinePM\Persisters\AuthorizationRequestPersister;
use TwinePM\Persisters\LoginSessionPersister;
class PersisterServiceProvider implements ServiceProviderInterface {
    function register(Container $container) {
        $container["authorizationRequestPersister"] = function () {
            $memoryDatabaseClient = $this->get("memoryDatabaseClient");
            $requestIdGetter = $this->get("requestIdGetter");
            $saltGetter = $this->get("saltGetter");
            $encryptionTransformer = $this->get("encryptionTransformer");
            return new AuthorizationRequestPersister(
                $memoryDatabaseClient,
                $requestIdGetter,
                $saltGetter,
                $encryptionTransformer);
        };

        $container["loginSessionPersister"] = function () {
            $memoryDatabaseClient = $this->get("memoryDatabaseClient");
            $requestIdGetter = $this->get("requestIdGetter");
            $saltGetter = $this->get("saltGetter");
            $encryptionTransformer = $this->get("encryptionTransformer");
            return new LoginSessionPersister(
                $memoryDatabaseClient,
                $requestIdGetter,
                $saltGetter,
                $encryptionTransformer);
        };
    }
}