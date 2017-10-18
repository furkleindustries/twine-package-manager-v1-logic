<?php
namespace TwinePM\Transformers;

use TwinePM\Responses;
interface ITransformer {
    function __invoke($value);
}