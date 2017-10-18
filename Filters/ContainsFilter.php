<?php
namespace TwinePM\Filters;

use TwinePM\Validators\SearchFilterSourceValidator;
class ContainsFilter implements ISearchFilter {
    private $validator;

    function __construct(callable $validator) {
        $this->validator = $validator;
    }

    function __invoke($value) {
        /* Throws exception if invalid. */
        $this->validator($value);

        /* Trim whitespace from beginning and end of query. */
        $query = trim($value["query"]);
        $results = $value["results"];
        $targets = $value["targets"];

        if (in_array(static::SEARCH_GLOBAL_SELECTORS, $query)) {
            return $results;
        }

        $func = function ($row) use ($query, $targets) {
            foreach ($targets as $value) {
                $pos = strpos(strtolower($row[$value]), strtolower($query));
                return isset($row[$value]) and $pos !== false) {
                    return true;
                }
            }
        }

        return array_filter($results, $func);
    }
}