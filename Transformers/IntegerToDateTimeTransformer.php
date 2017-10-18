<?php
namespace TwinePM\Transformers;

use TwinePM\Exceptions\ArgumentInvalidException;
class IntegerToDateTimeTransformer implements ITransformer {
    private $dateTimeGenerator;
    private $dateTimeOption;

    function __construct($dateTimeOption, callable $dateTimeGenerator) {
        $this->dateTimeOption = $dateTimeOption;
        $this->dateTimeGenerator = $dateTimeGenerator;
    }

    function __invoke($value) {
        if (gettype($value) !== "integer" or $value <= 0) {
            $errorCode = "IntegerTimestampInvalid";
            throw new ArgumentInvalidException($errorCode);
        }

        return $this->$dateTimeGenerator($dateTimeOption, $value);
    }
}