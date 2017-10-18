<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class CredentialSourceValidator implements IValidator {
    private $idFilter;
    private $nameValidator;

    function __construct(callable $idFilter, callable $nameValidator) {
        $this->$idFilter = $idFilter;
        $this->$nameValidator = $nameValidator;
    }

    function __invoke($value) {
        if (gettype($value) !== "array") {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("id", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["id"]);
        }

        if (array_key_exists("name", $value)) {
            /* Throws exception if invalid. */
            $this->$nameValidator($value["name"]);
        } else {
            $errorCode = "NameMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        $hash = isset($value["hash"]) ? $value["hash"] : null;
        if (array_key_exists("hash", $value)) {
            if (gettype($value["hash"]) !== "string") {
                $errorCode = "HashInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["hash"]) {
                $errorCode = "HashEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        }

        if (array_key_exists("validated", $value) and
            gettype($value["validated"]) !== "boolean")
        {
            $errorCode = "ValidatedInvalid";
            throw new ArgumentInvalidException($errorCode);
        }
    }
}