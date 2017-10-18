<?php
namespace TwinePM\OAuth2\Repositories;

use League\OAuth\Server\Repositories\ClientRepositoryInterface;
interface IClientRepository extends ClientRepositoryInterface {
    function getClientsNoSecrets(): array;
}