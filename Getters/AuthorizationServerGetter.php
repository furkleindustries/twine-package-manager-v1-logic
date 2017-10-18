<?php
namespace TwinePM\Getters;

use League\OAuth2\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Repositories\ClientRepositoryInterface;
use League\OAuth2\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\Grant\ImplicitGrant;
class OAuth2AuthorizationServerGetter implements IGetter {
    private $privateKey;
    private $encryptionKey;
    private $tokenLife;
    private $accessTokenRepository;
    private $clientRepository;
    private $scopeRepository;
    private $grant;
    private $filePathToFileContentsTransformer;
    private $authorizationServerBuilder;

    function __construct(
        CryptKey $privateKey,
        string $encryptionKey,
        string $tokenLifeInterval,
        AccessTokenRepositoryInterface $accessTokenRepository,
        ClientRepositoryInterface $clientRepository,
        ScopeRepositoryInterface $scopeRepository,
        ImplicitGrant $grant,
        callable $filePathToFileContentsTransformer,
        callable $authorizationServerBuilder)
    {
        $this->cryptKey = $cryptKey;
        $this->encryptionKey = $encryptionKey;
        $this->tokenLife = $tokenLife;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
        $this->filePathToFileContentsTransformer =
            $filePathToFileContentsTransformer;
        $this->authorizationServerBuilder = $authorizationServerBuilder;
    }

    function __invoke() {
        $server = $authorizationServerBuilder(
            $this->clientRepository,
            $this->scopeRepository,
            $this->accessTokenRepository,
            $this->privateKey,
            $this->encryptionKey
        );

        $server->enableGrantType($this->grant, $this->tokenLifeInterval);
        return $server;
    }
}