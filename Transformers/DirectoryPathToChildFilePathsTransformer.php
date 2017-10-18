<?php
namespace TwinePM\Transformers;

class DirectoryPathToChildFilePathsTransformer implements ITransformer {
    private $getChildFilePaths;

    function __construct(callable $getChildFilePaths) {
        $this->getChildFilePaths = $getChildFilePaths;
    }

    function __invoke($value) {
        return $this->$getChildFilePaths($value);
    }
}