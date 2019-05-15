<?php

namespace PhpureCore\Bootstrap;

use Exception;
use PhpureCore\Cargo;

class Foundation
{

    private $cargo = null;

    public function __construct(Cargo $cargo)
    {
        $this->cargo = $cargo;
        return $this;
    }

    /**
     * @param $dir
     * @param int $qty
     * @return int|void
     */
    private function requireDir($dir, $qty = 0)
    {
        if (!is_dir($dir)) return;
        $files = opendir($dir);
        while ($file = readdir($files)) {
            if ($file != '.' && $file != '..') {
                $realFile = $dir . '/' . $file;
                if (is_dir($realFile)) {
                    $qty = $this->requireDir($realFile, $qty);
                } elseif (strpos($file, '.php') === false) {
                    continue;
                } else {
                    require($realFile);
                    $qty++;
                }
            }
        }
        closedir($files);
        return $qty;
    }

    /**
     * 基础函数初始化
     * @throws Exception
     */
    public function init()
    {
        // cargo
        $path = realpath($this->cargo->getRoot() . DIRECTORY_SEPARATOR . 'foundation');
        if(!$path) throw new Exception('Foundation Error: root path');
        $qty = $this->requireDir($path);
        $this->cargo->setFoundationQty($qty);
        return $this->cargo;
    }
}