<?php
namespace TwinePM\Exceptions;

class ArgumentInvalidException extends AbstractTwinePmException {
    const DEFAULT_HTTP_STATUS = 400;
    const DEFAULT_ERROR_CODE = "ArgumentInvalid";
}