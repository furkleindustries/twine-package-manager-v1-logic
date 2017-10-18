<?php
namespace TwinePM\Filters;

class ExactFilter implements ISearchFilter {
    private $validator;

    function __construct(callable $validator) {
        $this->validator = $validator;
    }

    function __invoke($value) {
        $this->validator($value);

        $query = trim($value["query"]);
        $results = $source["results"];
        $targets = $source["targets"];

        if (in_array(static::SEARCH_GLOBAL_SELECTORS, $query)) {
            return $results;
        }

        $func = function ($row) use ($query, $targets) {
            foreach ($targets as $value) {
                $matches = strtolower($row[$value]) === strtolower($query);
                if (isset($row[$value]) and $matches) {
                    return true;
                }
            }

            return false;
        };

        return array_filter($results, $func);
    }
}