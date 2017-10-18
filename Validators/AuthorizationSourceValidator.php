<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class AuthorizationSourceValidator implements IValidator {
    private $clientIds;
    private $scopeIds;
    private $twinePmEpoch;
    private $idFilter;
    private $nameValidator;

    function __construct(
        array $clientIds,
        array $scopeIds,
        int $twinePmEpoch,
        callable $idFilter,
        callable $nameValidator)
    {
        $this->clientIds = $clientIds;
        $this->scopeIds = $scopeIds;
        $this->twinePmEpoch = $twinePmEpoch;
        $this->idFilter = $idFilter;
        $this->nameValidator = $nameValidator;
    }

    function __invoke($value): void {
        if (gettype($value) !== "array") {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("userId", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["userId"]);
        } else {
            $errorCode = "UserIdMissing";
            throw new ServerErrorException($errorCode);
        }

        if (!array_key_exists("client", $value)) {
            $errorCode = "ClientMissing";
            throw new ServerErrorException($errorCode);
        } else if (gettype($value["client"]) !== "string" or
            !array_key_exists($value["client"], $this->clientIds))
        {
            $errorCode = "ClientInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("scopes", $value)) {
            $scopes = $value["scopes"];
            if (gettype($scopes) !== "array") {
                $errorCode = "ScopesInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$scopes) {
                $errorCode = "ScopesEmpty";
                throw new ArgumentInvalidException($errorCode);
            }

            foreach ($scopes as $scope) {
                if (gettype($scope) !== "string") {
                    $errorCode = "ScopeInvalid";
                    throw new ServerErrorException($errorCode);
                } else if (!array_key_exists($scope, $this->scopeIds)) {
                    $errorCode = "ScopeInvalid";
                    throw new ArgumentInvalidException($errorCode);
                }
            }
        } else {
            $errorCode = "ScopesMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (!array_key_exists("ip", $value)) {
            $errorCode = "IpMissing";
            throw new ServerErrorException($errorCode);
        } else if (gettype($value["ip"]) !== "string" or !$value["ip"]) {
            $errorCode = "IpInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (!array_key_exists("oAuthToken", $value)) {
            $errorCode = "OAuthTokenMissing";
            throw new ServerErrorException($errorCode);
        } else if (gettype($value["oAuthToken"]) !== "string" or
            !$value["oAuthToken"])
        {
            $errorCode = "OAuthTokenInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("timeCreated", $value) and
            (gettype($value["timeCreated"]) !== "integer" or
                !($value["timeCreated"] > $this->twinePmEpoch)))
        {
            $errorCode = "TimeCreatedInvalid";
            throw new ServerErrorException($errorCode);
        }
    }
}