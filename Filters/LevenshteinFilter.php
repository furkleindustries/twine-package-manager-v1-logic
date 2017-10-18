<?php
namespace TwinePM\Filters;

class LevenshteinFilter implements ISearchFilter {
    const MAX_LEVENSHTEIN = 5;

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
                if (isset($row[$value])) {
                    $levenshtein = levenshtein($query, $row[$value]);
                    if ($levenshtein <= static::MAX_LEVENSHTEIN) {
                        return true;
                    }
                }
            }

            return false;
        };

        return array_filter($results, $func);
    }
}