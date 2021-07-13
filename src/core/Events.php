<?php

namespace PHPty;

class Events {

    private $hooks = [];
    private $actions = [];

    /**
     * Dispatch
     *
     * @param string $hook Hook name
     */
    public function dispatch( string $hook ) {
        // Get Args
        $args = func_get_args();

        // Remove hook name
        unset($args[0]);

        // Reset args array
        $args = array_values($args);

        if (array_key_exists($hook, $this->actions)) {
            foreach ($this->actions[$hook] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * On
     *
     * Hooks into a named event
     *
     * @param string $hook Hook name
     * @param callable $callback Callback to execute
     */
    public function on( string $hook, callable $callback ) {
        $this->actions[$hook][] = $callback;
    }

}