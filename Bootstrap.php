<?php
/**
 * Bootstrap core
 */

namespace PhpureCore;

use Exception;
use PhpureCore\Bootstrap\{Creator, Env, Type};

class Bootstrap
{

    private $type = Type::AJAX_HTTP;

    public function __construct(Creator $creator)
    {
        try {
            // 检测 | 配置设定
            (new Env($creator))->init();
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
        switch ($this->getType()){
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