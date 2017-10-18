<?php
namespace TwinePM\Persisters;

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Predis\Client;
class AuthorizationRequestPersister {
    private $memoryDatabaseClient;
    private $requestIdGetter;
    private $saltGetter;
    private $encryptionTransformer;

    function __construct(
        Client $memoryDatabaseClient,
        callable $requestIdGetter,
        callable $saltGetter,
        callable $encryptTransformer)
    {
        $this->memoryDatabaseClient = $memoryDatabaseClient;
        $this->requestIdGetter = $requestIdGetter;
        $this->saltGetter = $saltGetter;
        $this->encryptionTransformer = $encryptionTransformer;
    }

    function __invoke(
        string $domain,
        AuthorizationRequest $authRequest,
        int $maxGenerationAttempts = 5,
        int $expiryTimeInSeconds = 3600): void
    {
        if (!$domain) {
            $errorCode = "DomainInvalid";
            throw new InvalidArgumentException($errorCode);
        }

        $expires = time() + $expiryTimeInSeconds;

        $mdb = $this->memoryDatabaseClient;

        $salt = $this->saltGetter();
        $sar = serialize($authRequest);
        $cacheSession = [
            "salt" => $salt,
            "serializedAuthenticationRequest" => $sar,
            "expires" => $expires,
        ];

        /* Sort so deep-equal objects are serialized identically. */
        array_multisort($cacheSession);

        $encrypted = $this->encryptionTransformer(json_encode($cacheSession));
        $requestId = null;
        $key = "authorizationRequests";
        for ($ii = 0; $ii < $maxGenerationAttempts; $ii += 1) {
            $requestId = $this->requestIdGetter();
            if ($mdb->HSETNX($key, $requestId, $encrypted)) {
                $requestId = $field;
                break;
            }
        }

        if (!$requestId) {
            $errorCode = "RequestIdFailed";
            throw new GenerationFailedException($errorCode);
        }

        $cookie = [
            "requestId" => $requestId,
            "salt" => $salt,
        ];

        /* Sort so deep-equal objects are serialized identically. */
        array_multisort($cookie);

        $key = "authorizationRequest";
        $value = $this->encryptionTransformer(json_encode($cookie));
        $path = "/authorization";
        $result = null;
        if (getenv("TWINEPM_MODE") === "dev") {
            $result = setcookie(
                $key,
                $value,
                $expires);
        } else {
            $secure = true;
            $httpOnly = true;
            $result = setcookie(
                $key,
                $value,
                $expires,
                $path,
                $domain,
                $secure,
                $httpOnly);
        }

        if (!$result) {
            $errorCode = "AuthorizationSessionPersistence";
            throw new PersistenceFailedException($errorCode);
        }
    }

    function unpersist(string $requestId, string $domain): void {
        if (!$requestId) {
            $errorCode = "RequestIdInvalid";
            throw new InvalidArgumentException($errorCode);
        }

        if (!$domain) {
            $errorCode = "DomainInvalid";
            throw new InvalidArgumentException($errorCode);
        }

        $key = "authorizationRequests";
        /* Delete the key in the cache server. */
        $this->memoryDatabaseClient->HDEL($key, $requestId);

        $key = "authorizationSession";
        $value = "";
        $expires = -1;
        $path = "/authorization";
        $result = null;
        if (getenv("TWINEPM_MODE") === "dev") {
            $result = setcookie(
                $key,
                $value,
                $expires);
        } else {
            $secure = true;
            $httpOnly = true;
            $result = setcookie(
                $key,
                $value,
                $expires,
                $path,
                $domain,
                $secure,
                $httpOnly);
        }

        if (!$result) {
            $errorCode = "AuthorizationSessionPersistence";
            throw new UnpersistenceFailedException($errorCode);
        }
    }
}