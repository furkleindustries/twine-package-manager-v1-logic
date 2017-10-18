<?php
namespace TwinePM\Transformers;

use TwinePM\Exceptions\ArgumentInvalidException;
class PasswordHashTransformer implements ITransformer {
    private $hasher;
    private $hashOption;

    function __construct(callable $hasher, $hashOption) {
        $this->hasher = $hasher;
        $this->hashOption = $hashOption;
    }

    function __invoke($value) {
        if (!$value or gettype($value) !== "string") {
            $errorCode = "PasswordHashFailure";
            throw new ArgumentInvalidException($errorCode);
        }

        return $this->$hasher($value, $this->hashOption);
    }
}