<?php

namespace PHPty;

use Exception;

class Filters
{

    private $hooks = [];
    private $filters = [];

    /**
     * Add Filter
     */
    public function add( string $hook, $callback, int $priority = 10 ) {
        $this->filters[$hook][] = [
            'priority'  => $priority,
            'callback'  => $callback
        ];
    }

    /**
     * Filter Exists
     */
    public function filterExists( string $hook, string $name )
    {
        return isset($this->filters[$hook][$name]);
    }

    /**
     * Apply Filters
     */
    public function apply( string $hook, $input = null )
    {

        // Check for active filters
        if (empty($this->filters[$hook])) {
            return $input;
        }

        // Get Filters
        $filters = $this->filters[$hook];

        // Sort Filters by Priority
        $filters = $this->sortFilters($filters);

        // Apply Each Filter
        foreach ($filters as $filter) {
            $input = $this->applyFilter(
                $hook,
                $filter['callback'],
                $input
            );
        }

        // Return Filtered Result
        return $input;
    }

    /**
     * Apply Filter
     */
    public function applyFilter( string $hook, $callback, $input )
    {

        /**
         * Helper to allow filters to automatically
         * return a boolean value instead of having
         * to write a function to return the value
         *
         * e.g. $filters->add( 'render', false );
         */
        if ( is_bool($callback) ) {
            return $callback;
        }

        // Check Filter Function Exists
        if ( ! is_callable($callback) ) {
            throw new Exception('Invalid callback.');
        }

        $input = call_user_func($callback, $input);
        return $input;
    }

    /**
     * Sort Filters
     */
    protected function sortFilters( array $filters )
    {
        usort($filters, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }

            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });

        return $filters;
    }
}
