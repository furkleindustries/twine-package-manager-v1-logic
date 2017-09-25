<?php
namespace TwinePM\Getters;

class ServerDomainNameGetter {
    function __invoke(): string {
        return getenv("SERVER_URL");
    }
}