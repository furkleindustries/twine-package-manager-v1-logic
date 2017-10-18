<?php
namespace TwinePM\Transformers;

use Defuse\Crypto\Key;
use TwinePM\Exceptions\ArgumentInvalidException;
class DecryptionTransformer implements ITransformer {
    private $decrypter;
    private $key;

    function __construct(callable $decrypter, Key $key) {
        $this->decrypter = $decrypter;
        $this->key = $key;
    }

    function __invoke($value) {
        if (!$value or gettype($value) !== "string") {
            $errorCode = "EncryptionFailure";
            throw new ArgumentInvalidException($errorCode);
        }

        return $this->$decrypter($value, $this->key);
    }
}