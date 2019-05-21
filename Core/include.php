<?php
spl_autoload_register(function ($res){
    var_dump($res);
    $res = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $res);
    foreach ([PATH_APP, PATH_H_PHP, PATH_PLUGINS] as $path) {
        $file = $path . DIRECTORY_SEPARATOR . $res . PHP_EXT;
        if (is_file($file)) {
            require($file);
            break;
        }
    }
});