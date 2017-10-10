<?php
namespace TwinePM\ServiceProviders;

use Slim\ContainerInterface;
use Slim\DefaultServicesProvider;
use TwinePM\Loggers\AccessLogger;
use TwinePM\Loggers\ClientErrorLogger;
use TwinePM\Loggers\GenericErrorLogger;
use TwinePM\Loggers\LoggerRouter;
use TwinePM\Loggers\OAuthServerErrorLogger;
use TwinePM\Loggers\PermissionsErrorLogger;
use TwinePM\Loggers\ServerErrorLogger;
use TwinePM\Loggers\SqlErrorLogger;
class LoggerServiceProvider extends DefaultServicesProvider {
    function register(ContainerInterface $container) {
        $container["accessLogger"] = function () {
            return new AccessLogger();
        };

        $container["clientErrorLogger"] = function () {
            return new ClientErrorLogger();
        };

        $container["genericErrorLogger"] = function () {
            return new GenericErrorLogger();
        };

        $container["loggerRouter"] = function () {
            return new LoggerRouter();
        };

        $container["oAuthServerErrorLogger"] = function () {
            return new OAuthServerErrorLogger();
        };

        $container["permissionsErrorLogger"] = function () {
            return new PermissionsErrorLogger();
        };

        $container["serverErrorLogger"] = function () {
            return new ServerErrorLogger();
        };

        $container["sqlErrorLogger"] = function () {
            return new SqlErrorLogger();
        };
    }
}