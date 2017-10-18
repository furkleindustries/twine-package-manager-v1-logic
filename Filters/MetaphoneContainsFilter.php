<?php
namespace TwinePM\Filters;

class MetaphoneContainsFilter implements ISearchFilter {
    private $validator;

    function __construct(callable $validator) {
        $this->validator = $validator;
    }

    function __invoke($value) {
        /* Throws exception if invalid. */
        $this->validator($value);

        $query = trim($value["query"]);
        $results = $value["results"];
        $targets = $value["targets"];

        if (in_array(static::SEARCH_GLOBAL_SELECTORS, $query)) {
            return $results;
        }

        $func = function ($row) use ($query, $targets) {
            foreach ($targets as $value) {
                $pos = strpos(strtolower($row[$value]), strtolower($query));
                if (isset($row[$value]) and $pos !== false) {
                    return true;
                }
            }

            return false;
        };

        return array_filter($results, $func);
    }
}