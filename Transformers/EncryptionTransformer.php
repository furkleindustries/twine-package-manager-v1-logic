<?php
namespace TwinePM\Transformers;

use TwinePM\Exceptions\ArgumentInvalidException;
use Defuse\Crypto\Key;
class EncryptionTransformer implements ITransformer {
    private $encrypter;
    private $key;

    function __construct(callable $encrypter, Key $key) {
        $this->encrypter = $encrypter;
        $this->key = $key;
    }

    function __invoke($value) {
        if (!$value or gettype($value) !== "string") {
            $errorCode = "EncryptionFailure";
            throw new ArgumentInvalidException($errorCode);
        }

        return $this->$encrypter($value, $this->key);
    }
}