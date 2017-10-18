<?php
namespace TwinePM\Filters;

interface IFilter {
    function __invoke($value);
}