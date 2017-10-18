<?php
namespace TwinePM\Getters;

use ArrayAccess;
class AppSettingsGetter implements IGetter {
    private $environmentMode;

    function __construct(string $environmentMode) {
        $this->environmentMode = $environmentMode;
    }

    function __invoke() {
        $settings = [];
        if ($this->environmentMode === "dev") {
            $settings["displayErrorDetails"] = true;
        }

        return $settings;
    }
}