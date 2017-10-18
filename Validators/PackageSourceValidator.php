<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class PackageSourceValidator implements IValidator {
    private $packageTypes;
    private $idFilter;
    private $nameValidator;

    function __construct(
        array $packageTypes,
        callable $idFilter,
        callable $nameValidator)
    {
        $this->$packageTypes = $packageTypes;
        $this->$idFilter = $idFilter;
        $this->$nameValidator = $nameValidator;
    }

    function __invoke($value) {
        if (array_key_exists("id", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["id"]);
        }

        if (array_key_exists("name", $value)) {
            /* Throws exception if invalid. */
            $this->$nameValidator($value["name"]);
        } else {
            $errorCode = "NameMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("authorId", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["authorId"]);
        } else {
            $errorCode = "AuthorIdMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("ownerId", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value["ownerId"]);
        } else {
            $errorCode = "OwnerIdMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("description", $value)) {
            if (gettype($value["description"]) !== "string") {
                $errorCode = "DescriptionInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["description"]) {
                $errorCode = "DescriptionEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "DescriptionMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        $yesStrict = true;
        if (array_key_exists("type", $value)) {
            if (gettype($value["type"]) !== "string") {
                $errorCode = "TypeInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["type"] or
                !in_array($value["type"], $this->packageTypes, $yesStrict))
            {
                $errorCode = "TypeInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "TypeMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("currentVersion", $value)) {
            if (gettype($value["currentVersion"]) !== "string") {
                $errorCode = "CurrentVersionInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!$value["currentVersion"]) {
                $errorCode = "CurrentVersionEmpty";
                throw new ArgumentInvalidException($errorCode);
            }
        } else {
            $errorCode = "CurrentVersionMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("timeCreated", $value) and
            gettype($value["timeCreated"]) !== "integer")
        {
            $errorCode = "TimeCreatedInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("keywords", $value)) {
            if (gettype($value["keywords"]) !== "array") {
                $errorCode = "KeywordsInvalid";
                throw new ServerErrorException($errorCode);
            }

            foreach ($value["keywords"] as $keyword) {
                if (gettype($keyword) !== "string") {
                    $errorCode = "KeywordInvalid";
                    throw new ArgumentInvalidException($errorCode);
                } else if (!$keyword) {
                    $errorCode = "KeywordEmpty";
                    throw new ArgumentInvalidException($errorCode);
                }
            }
        }

        if (array_key_exists("tag", $value) and
            gettype($value["tag"]) !== "string")
        {
            $errorCode = "TagInvalid";
            throw new ServerErrorException($errorCode);
        }
    }
}