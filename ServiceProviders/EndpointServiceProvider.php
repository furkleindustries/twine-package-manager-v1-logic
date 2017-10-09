<?php
namespace TwinePM\ServiceProviders;

use Slim\Container;
use Slim\DefaultServicesProvider;
use TwinePM\Endpoints\AccountCreateEndpoint;
use TwinePM\Endpoints\AccountReadEndpoint;
use TwinePM\Endpoints\AccountUpdateEndpoint;
use TwinePM\Endpoints\AccountDeleteEndpoint;
use TwinePM\Endpoints\AuthorizationCreateEndpoint;
use TwinePM\Endpoints\AuthorizationHtmlEndpoint;
use TwinePM\Endpoints\ClientsHtmlEndpoint;

class EndpointServiceProvider extends DefaultServicesProvider {
    function register(Container $container) {
        $container["accountCreateEndpoint"] = function () {
            return new AccountUpdateEndpoint();
        };

        $container["accountReadEndpoint"] = function () {
            return new AccountReadEndpoint();
        };

        $container["accountUpdateEndpoint"] = function () {
            return new AccountCreateEndpoint();
        };

        $container["accountDeleteEndpoint"] = function () {
            return new AccountDeleteEndpoint();
        };

        $container["authorizationCreateEndpoint"] = function () {
            return new AuthorizationCreateEndpoint();
        };

        $container["authorizationHtmlEndpoint"] = function () {
            return new AuthorizationHtmlEndpoint();
        };

        $container["clientsHtmlEndpoint"] = function () {
            return new ClientsHtmlEndpoint();
        };
    }
}