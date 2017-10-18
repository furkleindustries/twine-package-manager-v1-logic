<?php
namespace TwinePM\Exceptions;

class FileDoesNotExistException extends AbstractTwinePmException {
    const DEFAULT_ERROR_CODE = "FileDoesNotExist";
    const DEFAULT_HTTP_STATUS = 500;
}