<?php
namespace TwinePM\Filters;

class SimilarityFilter implements ISearchFilter {
    const MIN_SIMILARITY = 45;

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

        if (in_array(self::SEARCH_GLOBAL_SELECTORS, $query)) {
            $success = new Responses\Response();
            $success->filtered = $results;
            return $results;
        }

        $func = function ($row) use ($query, $targets) {
            foreach ($targets as $value) {
                if (isset($row[$value])) {
                    $percent = null;
                    similar_text($query, $row[$value], $percent);
                    return $percent >= static::MIN_SIMILARITY;
                }
            }

            return false;
        };

        return array_filter($results, $func);
    }
}