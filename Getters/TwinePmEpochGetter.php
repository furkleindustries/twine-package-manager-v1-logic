<?php
namespace TwinePM\Getters;

/* The time at which TwinePM first went online, used mostly for validating
 * user-provided Unix timestamps. */
class TwinePmEpochGetter implements IGetter {
    function __invoke() {
        return 1508191450;
    }
}