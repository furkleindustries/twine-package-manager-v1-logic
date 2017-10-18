<?php
namespace TwinePM\OAuth2\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use TwinePM\OAuth2\Entities\ClientEntity;
class ClientRepository implements IClientRepository {
    private $clients = [];

    function __construct(
        string $clientDirectory,
        callable $clientEntityBuilder,
        callable $directoryPathToChildFilepathsTransformer,
        callable $filePathToFileContentsTransformer,
        callable $fileExistsValidator)
    {
        $entries = $filePathToFileContentsTransformer($clientDir);
        foreach ($entries as $entry) {
            $filePath = $clientDir . $entry;
            try {
                $fileExistsValidator();
                $contents = $filePathToFileContentsTransformer($filePath);
                $yesAssoc = true;
                $clientObject = json_decode($contents, $yesAssoc);
                if (gettype($clientObject) === "array") {
                    $identifier = $entry;
                    $dotPos = strrpos($entry, ".");
                    if ($dotPos !== false) {
                        $identifier = substr($entry, 0, $dotPos);
                    }

                    $this->clients[$identifier] = $clientObject;
                }
            } catch (Exception $e) {
                /* File is directory or loading failed, skip it.
                 * TODO: logging */
            }
        }
    }

    function getClientsNoSecrets(): array {
        $clients = $this->clients;
        foreach ($clients as $key => $value) {
            unset($clients[$key]["secret"]);
        }

        return $clients;
    }

    function getClientEntity(
        $clientIdentifier,
        $grantType,
        $clientSecret = null,
        $mustValidateSecret = true)
    {
        $clients = $this->clients;

        /* Check if client is registered. */
        if (!isset($this->clients[$clientIdentifier])) {
            return;
        }

        $client = $clients[$clientIdentifier];
        if ($mustValidateSecret and $client["isConfidential"]) {
            $realClientSecret = $clients[$clientIdentifier]["secret"];
            if ($clientSecret !== $realClientSecret) {
                return;
            }
        }

        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($clientIdentifier);
        $clientEntity->setName($client["name"]);

        $redirectUri = $client["domain"] . "/" . $client["redirectPath"];
        $clientEntity->setRedirectUri($redirectUri);
        return $clientEntity;
    }
}