<?php
namespace TwinePM\Transformers;

use TwinePM\Exceptions\ArgumentInvalidException;
use TwinePM\Exceptions\ServerErrorException;
use TwinePM\SqlAbstractions\Accounts\IAccount;
class AccountToProfileTransformer implements ITransformer {
    function __invoke($value) {
        $yesStrict = true;
        if (!in_array("IAccount", class_implements($value), $yesStrict)) {
            $errorCode = "ValueInvalid";
            throw new ServerErrorException($errorCode);
        }

        $profile = $value->toArray();
        if (!$profile["nameVisible"]) {
            unset($profile["name"]);
        } else if (!$profile["dateCreatedVisible"]) {
            unset($profile["dateCreated"]);
        } else if ($profile["emailVisible"]) {
            unset($profile["email"]);
        }

        unset($profile["nameVisible"]);
        unset($profile["dateCreatedVisible"]);
        unset($profile["emailVisible"]);

        return $profile;
    }
}