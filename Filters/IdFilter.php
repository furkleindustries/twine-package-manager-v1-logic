<?php
namespace TwinePM\Filters;

class IdFilter implements IFilter {
    function __invoke($value) {
        $type = gettype($value);
        if (($type !== "string" and $type !== "integer") or
            ($type === "string" and !ctype_digit($value)) or
            ($type === "integer" and $value < 0))
        {
            $errorCode = "IdInvalid";
            throw new ArgumentInvalidException($errorCode);
        }

        return (int)$value;
    }
}