<?php
namespace TwinePM\Filters;

class MetaphoneLevenshteinFilter implements ISearchFilter {
    const MAX_METAPHONE_LEVENSHTEIN = 5;

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
            $success = new Responses\Response();
            $success->filtered = $results;
            return $success;
        }

        $func = function ($row) use ($query, $targets) {
            foreach ($targets as $value) {
                if (isset($row[$value])) {
                    $metaphoneQuery = metaphone($query);
                    $metaphoneValue = metaphone($row[$value]);
                    $metaphoneLevenshtein = levenshtein(
                        $metaphoneQuery,
                        $metaphoneValue);

                    if ($metaphoneLevenshtein <=
                        static::MAX_METAPHONE_LEVENSHTEIN)
                    {
                        return true;
                    }
                }
            }
        };

        $filtered = array_filter($results, $func);

        $success = new Responses\Response();
        $success->filtered = $filtered;
        return $success;
    }
}