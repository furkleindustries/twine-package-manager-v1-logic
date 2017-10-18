<?php
namespace TwinePM\Getters;

class CryptKeyGetter implements IGetter {
    private $cryptKeyBuilder;
    private $cryptKeyPrivateKeyFilePath;
    function __construct(
        string $cryptKeyPrivateKeyFilePath,
        callable $cryptKeyBuilder)
    {
        $this->cryptKeyPrivateKeyFilePath = $cryptKeyPrivateKeyFilePath;
        $this->cryptKeyBuilder = $cryptKeyBuilder;
    }

    function __invoke() {
        return $this->cryptKeyBuilder($this->cryptKeyPrivateKeyFilePath);
    }
}