<?php
spl_autoload_register(function ($className) {
    $classVendors = [
        'AdwMinified',
        'ImageMimeTypeGuesser',
        'ImageOptimizer',
        'MatthiasMullie',
        'Shaun',
        'Symfony',
        'PHPWee',
        'JSMin',
        'Psr',
        'Patchwork',
        'WebPConvert',
    ];
    $file = '';
    foreach ($classVendors as $classVendor) {
        if (strpos($className, $classVendor) !== false) {
            $file = __DIR__ . '/lib/' . $className . '.php';
            break;
        }
    }
    if (!empty($file)) {
        $file = str_replace('\\', '/', $file);
        if (file_exists($file)) {
            require_once($file);
        }
    }
});
?>