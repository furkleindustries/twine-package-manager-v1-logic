<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
class AccountSourceValidator implements IValidator {
    private $dateStyles;
    private $timeStyles;
    private $idFilter;
    private $nameValidator;

    function __construct(
        array $dateStyles,
        array $timeStyles,
        callable $idFilter,
        callable $nameValidator)
    {
        $this->dateStyles = $dateStyles;
        $this->timeStyles = $timeStyles;
        $this->idFilter = $idFilter;
        $this->nameValidator = $nameValidator;
    }

    function __invoke($value): void {
        if (gettype($value) !== "array") {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        /* Accounts must have an ID because the ID is generated with the
         * credential and must be known at the time of creation or
         * modification. */
        if (array_key_exists("id", $value)) {
            /* Throws exception if invalid. */
            $this->$idFilter($value);
        } else {
            $errorCode = "IdMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("name", $value)) {
            /* Throws exception if invalid. */
            $this->$nameValidator($value["name"]);
        } else {
            $errorCode = "NameMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("nameVisible", $value) and
            gettype($value["nameVisible"]) !== "boolean")
        {
            $errorCode = "NameVisibleInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("description", $value) and
            gettype($value["description"]) !== "string")
        {
            $errorCode = "DescriptionInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("timeCreated", $value)) {
            if (gettype($value["timeCreated"]) !== "integer") {
                $errorCode = "TimeCreatedInvalid";
                throw new ServerErrorException($errorCode);
            } else if ($value["timeCreated"] <= 0) {
                $errorCode = "TimeCreatedInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        }

        if (array_key_exists("timeCreatedVisible", $value) and
            gettype($value["timeCreatedVisible"]) !== "boolean")
        {
            $errorCode = "TimeCreatedVisibleInvalid";
            throw new ServerErrorException($errorCode);
        }

        if (array_key_exists("email", $value)) {
            if (gettype($value["email"]) !== "string") {
                $errorCode = "EmailInvalid";
                throw new ServerErrorException($errorCode);
            }
        } else {
            $errorCode = "EmailMissing";
            throw new ArgumentInvalidException($errorCode);
        }

        if (array_key_exists("emailVisible", $value) and
            gettype($value["emailVisible"]) !== "boolean")
        {
            $errorCode = "EmailVisibleInvalid";
            throw new ServerErrorException($errorCode);
        }

        $yesStrict = true;
        if (array_key_exists("dateStyle", $value)) {
            $dateStyle = $value["dateStyle"];
            $dateStyles = $this->dateStyles;
            if (gettype($dateStyle) !== "string") {
                $errorCode = "DateStyleInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!in_array($dateStyle, $dateStyles, $yesStrict)) {
                $errorCode = "DateStyleInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        }

        if (array_key_exists("timeStyle", $value)) {
            $timeStyle = $value["timeStyle"];
            $timeStyles = $this->timeStyles;
            if (gettype($timeStyle) !== "string") {
                $errorCode = "TimeStyleInvalid";
                throw new ServerErrorException($errorCode);
            } else if (!in_array($timeStyle, $timeStyles, $yesStrict)) {
                $errorCode = "TimeStyleInvalid";
                throw new ArgumentInvalidException($errorCode);
            }
        }

        if (isset($value["homepage"]) and
            gettype($value["homepage"]) !== "string")
        {
            $errorCode = "HomepageInvalid";
            throw new ServerErrorException($errorCode);
        }
    }
}