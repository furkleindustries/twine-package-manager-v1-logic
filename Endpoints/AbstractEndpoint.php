<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
abstract class AbstractEndpoint implements IEndpoint {
    abstract function __invoke(Container $container): ResponseInterface;

    abstract function getOptionsObject(): array;

    function getOptionsJson(): string {
        return json_encode($this->getOptionsObject());
    }
}