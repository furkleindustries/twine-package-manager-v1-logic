<?php
namespace TwinePM\Exceptions;

class PermissionDeniedException extends AbstractTwinePmException {
    const DEFAULT_ERROR_CODE = "PermissionDenied";
    const DEFAULT_HTTP_STATUS = 403;
}