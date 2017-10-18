<?php
namespace TwinePM\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use TwinePM\Filters\ContainsFilter;
use TwinePM\Filters\ExactFilter;
use TwinePM\Filters\IdFilter;
use TwinePM\Filters\LevenshteinFilter;
use TwinePM\Filters\MetaphoneContainsFilter;
use TwinePM\Filters\MetaphoneLevenshteinFilter;
use TwinePM\Filters\SimilarityFilter;
use TwinePM\Filters\SoundexLevenshteinFilter;
class FilterServiceProvider implements ServiceProviderInterface {
    function register(Container $container) {
        $validator = $this->get("searchFilterSourceValidator");

        $container["containsFilter"] = function () use ($validator) {
            return new ContainsFilter($validator);
        };

        $container["exactFilter"] = function () use ($validator) {
            return new ExactFilter($validator);
        };

        $container["idFilter"] = function () use ($validator) {
            return new IdFilter($validator);
        };

        $container["levenshteinFilter"] = function () use ($validator) {
            return new LevenshteinFilter($validator);
        };

        $container["metaphoneContainsFilter"] = function () use ($validator) {
            return new MetaphoneContainsFilter($validator);
        };

        $container["similarityFilter"] = function () use ($validator) {
            return new MetaphoneContainsFilter($validator);
        };

        $container["soundexLevenshteinFilter"] = function () use ($validator) {
            return new SoundexLevenshteinFilter($validator);
        };
    }
}