<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class SearchFilterSourceValidator implements IValidator {
    function __invoke($value) {
        if (array_key_exists("query", $value)) {
            if (gettype($value["query"]) !== "string") {
                $errorCode = "QueryInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["query"]) {
                $errorCode = "QueryEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "QueryMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (!array_key_exists("results", $value)) {
            $errorCode = "ResultsMissing";
            throw new ArgumentInvalidException($errorCode);
        } else if (gettype($value["results"]) !== "array") {
            $errorCode = "ResultsInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("targets", $value)) {
            if (gettype($value["targets"]) !== "array") {
                $errorCode = "TargetsInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["targets"]) {
                $errorCode = "TargetsEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "TargetsMissing";
            throw new ArgumentInvalidException($errorCode);
        }
    }
}