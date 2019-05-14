<?php

namespace PhpureCore\Bootstrap;

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
     */
    private function requireDir($dir)
    {
        if (!is_dir($dir)) return;
        $files = opendir($dir);
        while ($file = readdir($files)) {
            if ($file != '.' && $file != '..') {
                $realFile = $dir . '/' . $file;
                if (is_dir($realFile)) {
                    $this->requireDir($realFile);
                } elseif (strpos($file, '.php') === false) {
                    continue;
                } else {
                    require_once($realFile);
                }
            }
        }
        closedir($files);
    }

    /**
     * 初始化
     */
    public function init()
    {
        // cargo
        // $this->cargo->setRoot($this->creator->getRoot());
        return $this->cargo;
    }
}