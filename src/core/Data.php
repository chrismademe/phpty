<?php

namespace Staple;

class Data {

    private $data = [];
    private $immutable = [];

    /**
     * Get
     *
     * @param string $key Variable name
     * @param mixed $default An optional default value if false
     */
    public function get($key = null, $default = null)
    {

        // Return All data
        if (is_null($key)) {
            return $this->data;
        }

        $parsed = explode('.', $key);

        $variable = $this->data;

        while ($parsed) {
            $next = array_shift($parsed);

            if (isset($variable[$next])) {
                $variable = $variable[$next];
            } else {
                $variable = null;
            }
        }

        return (is_null($variable) ? $default : $variable);
    }

    /**
     * Set
     */
    public function set($variable, $value = false, $immutable = false)
    {

        if (is_array($variable)) {
            foreach ($variable as $var => $val) {
                $this->set($var, $val, $immutable);
            }
        } elseif ($value !== false) {

            if (!in_array($variable, $this->immutable)) {

                $parsed = explode('.', $variable);

                $var = &$this->data;

                while (count($parsed) > 1) {
                    $next = array_shift($parsed);

                    if (!isset($var[$next]) || !is_array($var[$next])) {
                        $var[$next] = array();
                    }

                    $var = &$var[$next];
                }

                $var[array_shift($parsed)] = $value;

                if ($immutable === true) {
                    $this->immutable[] = $variable;
                }
            }
        }
    }

    public function delete($variable)
    {
        if (is_array($variable)) {
            foreach ($variable as $var) {
                $this->delete($var);
            }
        } else {
            if (!in_array($variable, $this->immutable) && array_key_exists($variable, $this->data)) {
                unset($this->data[$variable]);
            }
        }
    }

}