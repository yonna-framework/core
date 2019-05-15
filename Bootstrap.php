<?php
/**
 * Bootstrap core
 */

namespace PhpureCore;

use Exception;
use PhpureCore\Bootstrap\{Config, Creator, Env, Foundation, Type};
use PhpureCore\IO\Request;

class Bootstrap
{

    private $cargo = null;
    private $type = Type::AJAX_HTTP;

    public function __construct(Creator $creator)
    {
        $this->cargo = (new Cargo());
        try {
            // 环境初始化
            $this->cargo = (new Env($this->cargo, $creator))->init();
            // 基础功能
            $this->cargo = (new Foundation($this->cargo))->init();
            // 配置
            $this->cargo = (new Config($this->cargo))->init();

        } catch (Exception $e) {
            dd($e);
        }
        return $this;
    }

    /**
     * @return string
     */
    private function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function io(): void
    {
        // set type
        $this->cargo->setBootType($this->getType());
        // IO
        $Request = (new Request($this->cargo))->build();
        $response = (new IO($Request));

    }

}