<?php

namespace Yonna\Scope;

use Yonna\Core;
use Yonna\Throwable\Exception;

/**
 * Class Middleware
 * @package Core\Core\Scope
 */
abstract class Scope extends Kernel
{

    /**
     * @param string $call
     * @param string $action
     * @return mixed
     * @throws Exception\ThrowException
     */
    public function axis(string $call, string $action)
    {
        $Scope = Core::get($call, $this->request());
        if (!$Scope instanceof Scope) {
            Exception::throw("Class {$call} is not instanceof Scope");
        }
        return $Scope->$action();
    }

}