<?php 

// Prints Array
if (!function_exists('pa')){
    function pa($array) {
        echo '<pre>'; var_export($array); echo '</pre>';
    }
}

// Prints Array and stops further execution
if (!function_exists('dd')){
    function dd($array) {
        echo '<pre>'; var_export($array); echo '</pre>'; exit;
    }
}

/**
 * Returns the value for a key in an array or a property in an object.
 * Typical usage:
 * 
 * $object->foo = 'Bar';
 * echo get_key($object, 'foo');
 * 
 * $array['baz'] = 'Bat';
 * echo get_key($array, 'baz');
 */
if (!function_exists('get_key')){
    function get_key ($haystack, $needle, $default_value = '') {
        if (is_array($haystack)) {
            // We have an array. Find the key.
            return isset($haystack[$needle]) ? $haystack[$needle] : $default_value;
        }
        else {
            // If it's not an array it must be an object
            return isset($haystack->$needle) ? $haystack->$needle : $default_value;
        }
    }
}

if (!function_exists('get_val')){
    function get_val ($value, $default_value = '') {
        return isset($value) ? $value : $default_value;
    }
}

if (!function_exists('todayExceeds')){
    function todayExceeds($date) {
        $diff = strtotime(date('Y-m-d')) - strtotime($date);
        return $diff < 0 ? true : false;
    }
}