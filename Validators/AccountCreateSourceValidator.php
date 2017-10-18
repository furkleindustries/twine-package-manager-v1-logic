<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class AccountCreateSourceValidator {
    function __invoke($value): void {
        if (gettype($value) !== "array") {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("email", $value)) {
            if (gettype($value["email"]) !== "string") {
                $errorCode = "EmailInvalid";
                throw new ArgumentInvalidException($errorCode);
            } else if (!$value["email"]) {
                $errorCode = "EmailMissing";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "EmailMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("name", $value)) {
            if (gettype($value["name"]) !== "string") {
                $errorCode = "NameInvalid";
                throw new ArgumentInvalidException($errorCode);
            } else if (!$value["name"]) {
                $errorCode = "NameEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "NameMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("password", $value)) {
            if (gettype($value["password"]) !== "string") {
                $errorCode = "PasswordInvalid";
                throw new ArgumentInvalidException($errorCode);
            } else if (!$value["password"]) {
                $errorCode = "PasswordEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "PasswordMissing";
            throw new ArgumentInvalidException($errorCode);
        }
    }
}