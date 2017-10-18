<?php
namespace TwinePM\Validators;

interface IValidator {
    function __invoke($value);
}