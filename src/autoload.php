<?php

define('PK_SDK_ROOT_PATH', dirname(__FILE__));
define('PK_SDK_PSR_LOG_PATH', dirname(__FILE__).'/../vendor/psr/log/Psr/Log');

function pkSdkLoadClass($className)
{
    if (strncmp('PayKassa', $className, 12) === 0) {
        $path   = PK_SDK_ROOT_PATH;
        $length = 12;
    } elseif (strncmp('Psr\Log', $className, 7) === 0) {
        $path   = PK_SDK_PSR_LOG_PATH;
        $length = 7;
    } else {
        return;
    }
    $path .= str_replace('\\', '/', substr($className, $length)) . '.php';

    if (file_exists($path)) {
        require $path;
    }
}

spl_autoload_register('pkSdkLoadClass');
