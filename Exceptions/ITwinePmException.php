<?php
namespace TwinePM\Exceptions;

interface ITwinePmException {
    function __construct(
        string $errorCode = null,
        int $httpStatus = null);

    function getErrorCode(): string;
}