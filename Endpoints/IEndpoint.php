<?php
namespace TwinePM\Endpoints;

use Psr\Http\Message\ResponseInterface;
use Slim\ContainerInterface;
interface IEndpoint {
    function __invoke(Container $container): ResponseInterface;

    function getOptionsObject(): array;

    function getOptionsJson(): string;
}