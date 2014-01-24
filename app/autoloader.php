<?php
/**
 * @author AlexanderC <self@alexanderc.me>
 * @date 1/24/14
 * @time 10:42 AM
 */

spl_autoload_register(function ($class) {
        $path = __DIR__ . '/../';

        $parts = explode("\\", $class);

        if(count($parts) <= 0 || $parts[0] !== "Jabberd2") {
            return false;
        }

        $file = realpath($path . implode("/", $parts) . ".php");

        return is_file($file) ? require $file : false;
    });