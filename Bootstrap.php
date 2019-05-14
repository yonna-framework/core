<?php
/**
 * Bootstrap core
 */

namespace PhpureCore;

use Exception;
use PhpureCore\Bootstrap\{Creator, Env, Foundation, Type};

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

        } catch (Exception $e) {
            exit($e->getMessage());
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
        $this->cargo->setBootType($this->getType());
        switch ($this->getType()) {
            case Type::AJAX_HTTP:
                break;
            case Type::SWOOLE_HTTP:
                break;
            case Type::SWOOLE_WEB_SOCKET:
                break;
            case Type::SWOOLE_TCP:
                break;
            default:
                exit('Error IO');
                break;
        }
    }

}