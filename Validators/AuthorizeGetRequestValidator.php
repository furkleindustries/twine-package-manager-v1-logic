<?php
namespace TwinePM\Validators;

use Psr\Http\Message\ServerRequestInterface;
use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class AuthorizeGetRequestValidator implements IValidator {
    private $clientIds;
    private $scopeIds;

    function __construct(array $clientIds, array $scopeIds) {
        $this->clientIds = $clientIds;
        $this->scopeIds = $scopeIds;
    }

    function __invoke($value) {
        if (gettype($value) !== "array") {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("response_type", $value)) {
            if (gettype($value["response_type"]) !== "string") {
                $errorCode = "ResponseTypeInvalid";
                throw new ServerErrorException($errorCode);
            } else if ($value["response_type"] !== "token") {
                $errorCode = "ResponseTypeInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "ResponseTypeMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        $yesStrict = true;
        if (array_key_exists("client_id", $value)) {
            $clientId = $value["client_id"];
            if (gettype($clientId) !== "string") {
                $errorCode = "ClientIdInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!in_array($clientId, $this->clientIds, $yesStrict)) {
                $errorCode = "ScopeInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "ClientIdMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("scope", $value)) {
            $scopeId = $value["scope"];
            if (gettype($scopeId) !== "string") {
                $errorCode = "ScopeInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!in_array($scopeId, $this->scopeIds, $yesStrict)) {
                $errorCode = "ScopeInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "ScopeMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("state", $value)) {
            if (gettype($value["state"]) !== "string") {
                $errorCode = "StateInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value) {
                $errorCode = "StateEmpty";
                throw new ServerErrorException($errorCode);
            }
        } else {
            $errorCode = "StateMissing";
            throw new ServerErrorException($errorCode);
        }
    }
}