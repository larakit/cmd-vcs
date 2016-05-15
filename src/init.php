<?php
//регистрируем провайдеры
Larakit\Boot::register_command(\Larakit\Cmdvcs\CommandVcs::class);

if (!function_exists('larasafepath')) {
    function larasafepath($path) {
        $path = str_replace(['\\', '/'], '/', $path);
        $base_path = str_replace(['\\', '/'], '/', base_path());
        $path = str_replace($base_path, '', $path);
        return $path;
    }
}