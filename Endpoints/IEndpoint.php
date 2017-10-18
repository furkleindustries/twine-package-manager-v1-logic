<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\Container;
interface IEndpoint {
    function __invoke(Container $container): ResponseInterface;

    function getOptionsObject(): array;

    function getOptionsJson(): string;
}