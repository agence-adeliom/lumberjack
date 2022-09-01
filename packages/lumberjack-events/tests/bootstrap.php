<?php

$vendor = __DIR__ . '/../vendor/';

if (!file_exists($vendor)) {
    print_r('Please run composer install before triggering the tests.');
    exit();
}

require_once $vendor . 'autoload.php';

if (!function_exists('execute_func')) {
    function execute_func($function)
    {
        if (is_array($function)) {
            return call_user_func_array($function, []);
        }

        return $function();
    }
}
