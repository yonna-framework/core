<?php

namespace PhpureCore\Bootstrap;

use System;

class Functions
{

    public static function install(Cargo $Cargo)
    {
        $path = realpath($Cargo->getRoot() . DIRECTORY_SEPARATOR . 'functions');
        if ($path) {
            $qty = System::requireDir($path);
            $Cargo->setFunctionQty($qty);
        }
        return $Cargo;
    }

}