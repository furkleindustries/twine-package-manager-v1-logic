<?php
namespace TwinePM\Filters;

interface ISearchFilter extends IFilter {
    const SEARCH_GLOBAL_SELECTORS = [
        "",
        "*"
    ];

    function __construct(callable $validator);
} 