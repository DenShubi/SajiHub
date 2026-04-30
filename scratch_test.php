<?php
require __DIR__ . '/vendor/autoload.php';

echo "Checking for Laravel\Sanctum\HasApiTokens...\n";

if (trait_exists('Laravel\Sanctum\HasApiTokens')) {
    echo "SUCCESS: Trait found!\n";
} else {
    echo "FAILURE: Trait NOT found!\n";
    
    $path = __DIR__ . '/vendor/laravel/sanctum/src/HasApiTokens.php';
    if (file_exists($path)) {
        echo "File exists at: $path\n";
        require_once $path;
        if (trait_exists('Laravel\Sanctum\HasApiTokens')) {
            echo "Trait found AFTER manual require!\n";
        } else {
            echo "Trait STILL NOT found after manual require! Something is very wrong.\n";
        }
    } else {
        echo "File DOES NOT EXIST at: $path\n";
    }
}
