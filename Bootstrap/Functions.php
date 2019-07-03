<?php

namespace Yonna\Bootstrap;

use Yonna\Foundation\System;

class Functions
{

    public static function install(Cargo $Cargo): Cargo
    {
        $path = realpath($Cargo->getRoot() . DIRECTORY_SEPARATOR . 'functions');
        if ($path) {
            $qty = System::requireDir($path);
            $Cargo->setFunctionQty($qty);
        }
        return $Cargo;
    }

}