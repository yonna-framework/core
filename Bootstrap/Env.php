<?php
/**
 * Bootstrap ENV Checker
 */

namespace PhpureCore\Bootstrap;

class Env
{

    private $fail = '';

    protected function fail(string $msg)
    {
        $this->fail = $msg;
        return false;
    }

    protected function getFail(): string
    {
        return $this->fail;
    }

    /**
     * 环境初始化
     */
    private function init(){
        define('IS_WINDOW', strstr(PHP_OS, 'WIN') && PHP_OS !== 'CYGWIN' ? true : false);
        define('MEMORY_LIMIT_ON', function_exists('memory_get_usage'));
    }

    /**
     *  检测PHP版本
     */
    private function checkPHPVersion($version = '7.2')
    {
        if (version_compare(PHP_VERSION, $version, '<')){
            return $this->fail('');
        }
        return true;
    }

    /**
     * 综合检查
     * @throws \Exception
     */
    public function check()
    {
        if(!$this->checkPHPVersion()) throw new \Exception($this->fail);

        $this->init();
    }


}