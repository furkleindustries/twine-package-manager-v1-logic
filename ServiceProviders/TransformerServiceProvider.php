<?php
namespace TwinePM\ServiceProviders;

use DateTime;
use Defuse\Crypto;
use Defuse\Crypto\Key;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TwinePM\Transformers\AccountToProfileTransformer;
use TwinePM\Transformers\AuthorizationToTemplatingArrayTransformer;
use TwinePM\Transformers\DecryptionTransformer;
use TwinePM\Transformers\EncryptionTransformer;
use TwinePM\Transformers\IntegerToDateTimeTransformer;
use TwinePM\Transformers\PasswordHashTransformer;
class EndpointServiceProvider implements ServiceProviderInterface {
    function register(Container $container) {
        $container["accountToProfileTransformer"] = function () {
            return new AccountToProfileTransformer();
        };

        $container["authorizationToTemplatingArrayTransformer"] = function () {
            $clientRepository = $this->get("clientRepository");
            $scopeRepository = $this->get("scopeRepository");
            $dateTimeTransformer = $this->get("dateTimeTransformer");
            return new AuthorizationToTemplatingArrayTransformer(
                $clientRepository,
                $scopeRepository,
                $dateTimeTransformer);
        };

        $container["integerToDateTimeTransformer"] = function () {
            $dateTimeOption = DateTime::COOKIE;
            $dateTimeGenerator = function ($dateTimeOption, $value) {
                return date($dateTimeOption, $value);
            };

            return new IntegerToDateTimeTransformer(
                $dateTimeOption,
                $dateTimeGenerator);
        };

        $container["decryptionTransformer"] = function() {
            $decrypter = function (string $cipherText, Key $key) {
                return Crypto::decrypt($cipherText, $key);
            };

            return new DecryptionTransformer($decrypter, $this->get("key"));
        };

        $container["encryptionTransformer"] = function () {
            $encrypter = function (string $plainText, Key $key) {
                return Crypto::encrypt($plainText, $key);
            };

            return new EncryptionTransformer($encrypter, $this->get("key"));
        };

        $container["hashTransformer"] = function () {
            $hasher = function (string $plainText, $hashOption) {
                return password_hash($plainText, $hashOption);
            };

            return new PasswordHashTransformer($hasher, PASSWORD_DEFAULT);
        };
    }
}