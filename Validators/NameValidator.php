<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class NameValidator implements IValidator {
    function __invoke($value) {
        if ($value !== null) {
            if (gettype($value) !== "string") {
                $errorCode = "NameInvalid";
                throw new ServerErrorException($errorCode);
            } else if (gettype($value) === "string" and !$value) {
                $errorCode = "NameEmpty";
                throw new ArgumentInvalidException($errorCode);
            } else if (ctype_digit($value)) {
                $errorCode = "NameOnlyNumbers";
                throw new ArgumentInvalidException($errorCode);
            }
        }
    }
}