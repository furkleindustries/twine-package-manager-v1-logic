<?php
namespace TwinePM\Getters;

use Defuse\Crypto\Key;
use Exception;
class RequestIdKeyGetter implements IGetter {
    private $stringToKeyTransformer;
    private $filePathToFileContentsTransformer;
    private $writeToFile;
    private $keyGetter;
    private $keyBuilder;

    function __construct(
        callable $stringToKeyTransformer,
        callable $filePathToFileContentsTransformer,
        callable $filePersister,
        callable $keyGetter,
        callable $keyBuilder)
    {
        $this->stringToKeyTransformer = $stringToKeyTransformer;
        $this->filePathToFileContentsTransformer =
            $filePathToFileContentsTransformer;
        $this->filePersister = $filePersister;
        $this->keyGetter = $keyGetter;
        $this->keyBuilder = $keyBuilder;
    }

    function __invoke() {
        $contents = null;
        $key = null;
        try {
            $contents = $this->filePathToFileContentsTransformer($filepath);
        } catch (ITwinePmException $e) {
            $filepath = __DIR__ . "/../crypto/requestIdKey";
            $key = $this->keyGetter();
            try {
                $this->filePersister($filepath, $key->saveToAsciiSafeString());
            } catch (Exception $e) {
                $errorCode = "KeyPersistenceFailed";
                throw new PersistenceFailedException($errorCode);
            }
        }

        if ($contents) {
            $key = $this->keyBuilder($contents);
        }

        return $key;
    }
}