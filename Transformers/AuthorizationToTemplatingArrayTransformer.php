<?php
namespace TwinePM\Transformers;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\SqlAbstractions\Authorizations\IAuthorization;
use mixed;
class AuthorizationToTemplatingArrayTransformer implements ITransformer {
    private $clients;
    private $scopes;
    private $dateTimeOption;
    private $dateTimeTransformer;

    function __construct(
        array $clients,
        array $scopes,
        mixed $dateTimeOption,
        callable $dateTimeTransformer)
    {
        $this->clients = $clients;
        $this->scopes = $scopes;
        $this->dateTimeOption = $dateTimeOption;
        $this->dateTimeTransformer = $dateTimeTransformer;
    }

    function __invoke($value) {
        if (!($value instanceof IAuthorization)) {
            $errorCode = "ValueInvalid";
            throw new ArgumentInvalidException($errorCode);
        }

        $clientId = $value->getClient();
        $clientName = isset($this->clients[$clientId]) ?
            $this->clients[$clientId]["name"] : "CLIENT_NAME_ERROR";

        $dateTimeOption = $this->dateTimeOption;
        $timeCreated = $value->getTimeCreated();
        $dateTime = $this->$dateTimeTransformer($dateTimeOption, $timeCreated);
        $scopes = array_map(function ($scopeId) {
            return array_key_exists($scopeId, $this->scopes) ?
                $this->scopes[$scopeId]["name"] : "SCOPE_ERROR";
        }, $value->getScopes());

        return [
            "globalAuthorizationId" => $value->getGlobalAuthorizationId(),
            "clientName" => $clientName,
            "dateTime" => $dateTime,
            "scopes" => implode(", ", $scopes),
            "ip" => $value->getIp(),
        ];
    }
}