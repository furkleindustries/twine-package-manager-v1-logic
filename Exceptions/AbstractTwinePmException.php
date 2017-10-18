<?php
namespace TwinePM\Exceptions;

use Exception;
class AbstractTwinePmException extends Exception implements ITwinePmException {
    const DEFAULT_HTTP_STATUS = 500;
    const DEFAULT_ERROR_CODE = "UnspecifiedError";

    function __construct(
        string $errorCode = null,
        int $httpStatus = null)
    {
        $ec = $errorCode ?? static::DEFAULT_ERROR_CODE;
        $status = $httpStatus ?? static::DEFAULT_HTTP_STATUS;

        /* Use the error code as the exception message. */
        parent::__construct($ec, $status);
        $this->errorCode = $errorCode;
    }

    function getErrorCode() {
        return $this->errorCode;
    }
}