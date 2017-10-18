<?php
namespace TwinePM\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TwinePM\Endpoints\AccountCreateEndpoint;
use TwinePM\Endpoints\AccountReadEndpoint;
use TwinePM\Endpoints\AccountUpdateEndpoint;
use TwinePM\Endpoints\AccountDeleteEndpoint;
use TwinePM\Endpoints\AuthorizationCreateEndpoint;
use TwinePM\Endpoints\AuthorizeHtmlEndpoint;
use TwinePM\Endpoints\ClientsHtmlEndpoint;
use TwinePM\Endpoints\LoginHtmlEndpoint;
use TwinePM\Endpoints\LoginCreateEndpoint;
use TwinePM\Endpoints\LoginDeleteEndpoint;
use TwinePM\Endpoints\LogoutHtmlEndpoint;
use TwinePM\Endpoints\PackageCreateEndpoint;
use TwinePM\Endpoints\PackageReadEndpoint;
use TwinePM\Endpoints\PackageUpdateEndpoint;
use TwinePM\Endpoints\PackageDeleteEndpoint;
use TwinePM\Endpoints\PackageSearchEndpoint;
use TwinePM\Endpoints\ProfileReadEndpoint;
use TwinePM\Endpoints\ProfileSearchEndpoint;
use TwinePM\Endpoints\RootHtmlEndpoint;
use TwinePM\Endpoints\SearchEndpoint;
use TwinePM\Endpoints\UnauthorizeHtmlEndpoint;
use TwinePM\Endpoints\UserOptionsHtmlEndpoint;
use TwinePM\Endpoints\ValidateEmailEndpoint;
class EndpointServiceProvider implements ServiceProviderInterface {
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

        $container["authorizeHtmlEndpoint"] = function () {
            return new AuthorizeHtmlEndpoint();
        };

        $container["clientsHtmlEndpoint"] = function () {
            return new ClientsHtmlEndpoint();
        };

        $container["loginHtmlEndpoint"] = function () {
            return new LoginHtmlEndpoint();
        };

        $container["loginCreateEndpoint"] = function () {
            return new LoginCreateEndpoint();
        };

        $container["loginDeleteEndpoint"] = function () {
            return new LoginDeleteEndpoint();
        };

        $container["logoutHtmlEndpoint"] = function () {
            return new LogoutHtmlEndpoint();
        };

        $container["packageCreateEndpoint"] = function () {
            return new PackageCreateEndpoint();
        };

        $container["packageReadEndpoint"] = function () {
            return new PackageReadEndpoint();
        };

        $container["packageUpdateEndpoint"] = function () {
            return new PackageUpdateEndpoint();
        };

        $container["packageDeleteEndpoint"] = function () {
            return new PackageDeleteEndpoint();
        };

        $container["packageSearchEndpoint"] = function () {
            return new PackageSearchEndpoint();
        };

        $container["profileReadEndpoint"] = function () {
            return new ProfileReadEndpoint();
        };

        $container["profileSearchEndpoint"] = function () {
            return new ProfileSearchEndpoint();
        };

        $container["rootHtmlEndpoint"] = function () {
            return new RootHtmlEndpoint();
        };

        $container["searchEndpoint"] = function () {
            return new SearchEndpoint();
        };

        $container["unauthorizeHtmlEndpoint"] = function () {
            return new UnauthorizeHtmlEndpoint();
        };

        $container["userOptionsHtmlEndpoint"] = function () {
            return new UserOptionsHtmlEndpoint();
        };

        $container["validateEmailEndpoint"] = function () {
            return new ValidateEmailEndpoint();
        };
    }
}