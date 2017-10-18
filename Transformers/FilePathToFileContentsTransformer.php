<?php
namespace TwinePM\Transformers;

class FilePathToFileContentsTransformer {
    private $getFileContents;

    function __construct(callable $getFileContents) {
        $this->getFileContents = $getFileContents;
    }

    function __invoke($value) {
        return $this->$getFileContents($value);
    }
}