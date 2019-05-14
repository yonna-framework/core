<?php

namespace PhpureCore\Foundation;

class Foundation
{

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

}