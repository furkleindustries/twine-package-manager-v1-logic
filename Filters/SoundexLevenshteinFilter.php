<?php
namespace TwinePM\Filters;

class SoundexLevenshteinFilter implements ISearchFilter {
    const MAX_SOUNDEX_LEVENSHTEIN = 5;

    private $validator;

    function __construct(callable $validator) {
        $this->validator = $validator;
    }

    function filter($value) {
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
                    $soundexQuery = soundex($value["query"]);
                    $soundexValue = soundex($row[$value]);
                    $soundexLevenshtein = levenshtein(
                        $soundexQuery,
                        $soundexValue);

                    $max = static::MAX_SOUNDEX_LEVENSHTEIN;
                    return $soundexLevenshtein < $max;
                }
            }
        }

        return array_filter($results, $func);
    }
}