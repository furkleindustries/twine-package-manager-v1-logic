<?php
namespace TwinePM\Exceptions;

class ServerErrorException extends AbstractTwinePmException {
    const DEFAULT_ERROR_CODE = "ServerError";
    const DEFAULT_HTTP_STATUS = 500;
} 