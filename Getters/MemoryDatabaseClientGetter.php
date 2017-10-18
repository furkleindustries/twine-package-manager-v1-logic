<?php
namespace TwinePM\Getters;

class MemoryDatabaseClientGetter {
    private $url;
    private $clientBuilder;

    function __construct(string $url, callable $clientBuilder) {
        $this->url = $url;
        $this->clientBuilder = $clientBuilder;
    }

    function __invoke() {
        return $this->clientBuilder($this->url);
    }
}