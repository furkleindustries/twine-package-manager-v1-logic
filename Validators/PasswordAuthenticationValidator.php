<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\PermissionDeniedException;
use TwinePM\Exceptions\UserRequestFieldInvalidException;
class PasswordAuthenticationValidator implements IValidator {
    private $passwordAuthenticator;

    function __construct(callable $passwordAuthenticator) {
        $this->$passwordAuthenticator = $passwordAuthenticator;
    }

    function __invoke($value) {
        if (!array_key_exists("password", $value)) {
            if (gettype($value["password"]) !== "string") {
                $errorCode = "PasswordInvalid";
                throw new ArgumentInvalidException($errorCode);
            } else if (!$value["password"]) {
                $errorCode = "PasswordEmpty";
                throw new UserRequestFieldInvalidException($errorCode);
            }
        } else {
            $errorCode = "PasswordMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("hash", $value) and
            (!$value["hash"] or gettype($value["hash"]) !== "string"))
        {
            $errorCode = "HashInvalid";
            throw new ArgumentInvalidException($errorCode);
        } else {
            $errorCode = "HashMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        $pass = $value["password"];
        $hash = $value["hash"];
        if (!$this->$passwordAuthenticator($pass, $hash)) {
            throw new PermissionDeniedException();
        }
    }
}