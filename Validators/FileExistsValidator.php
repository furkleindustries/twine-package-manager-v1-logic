<?php
namespace TwinePM\Validators;

use TwinePM\Exceptions\FileDoesNotExistException;
class FileExistsValidator implements IValidator {
    private $fileExists;

    function __construct(callable $fileExists) {
        $this->$fileExists = $fileExists;
    }

    function __invoke($value): void {
        if (!$this->$fileExists($value)) {
            throw new FileDoesNotExistException();
        }
    }
}