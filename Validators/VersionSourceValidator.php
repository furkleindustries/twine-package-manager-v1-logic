<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class VersionSourceValidator implements IValidator {
    private $idFilter;
    private $nameValidator;

    function __construct(
        callable $idFilter,
        callable $nameValidator)
    {
        $this->$idFilter = $idFilter;
        $this->$nameValidator = $nameValidator;
    }

    function __invoke($value) {
        if (array_key_exists("packageId", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["packageId"]);
        }

        if (array_key_exists("name", $value)) {
            /* Throws exception if invalid. */
            $this->$nameValidator($value["name"]);
        } else {
            $errorCode = "NameMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("version", $value)) {
            if (gettype($value["version"]) !== "string") {
                $errorCode = "VersionInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["version"]) {
                $errorCode = "VersionEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "VersionMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("js", $value) and
            gettype($value["js"]) !== "string")
        {
            $errorCode = "JsInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("css", $value) and
            gettype($value["css"]) !== "string")
        {
            $errorCode = "CssInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("keywords", $value)) {
            if (gettype($value["keywords"]) !== "array") {
                $errorCode = "KeywordsInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["keywords"]) {
                $errorCode = "KeywordsEmpty";
                throw new ArgumentInvalidException($errorCode);
            }

            foreach ($value["keywords"] as $value) {
                if (gettype($value) !== "string") {
                    $errorCode = "KeywordInvalid";
                    throw new ServerErrorException($errorCode);
                } else if (!$value) {
                    $errorCode = "KeywordEmpty";
                    throw new ArgumentInvalidException($errorCode);
                }
            }
        }

        if (array_key_exists("description", $value)) {
            if (gettype($value["description"]) !== "string") {
                $errorCode = "DescriptionInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["description"]) {
                $errorCode = "DescriptionInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "DescriptionMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (isset($value["homepage"]) and
            gettype($value["homepage"]) !== "string")
        {
            $errorCode = "HomepageInvalid";
            throw new ServerErrorException($errorCode);
        }
    }
}