<?php

namespace Yonna\Scope;

/**
 * Class Kernel
 * @package Core\Core\Scope
 */
abstract class Kernel implements Interfaces\Kernel
{

    /**
     * @var \Yonna\IO\Request $request
     */
    private $request = null;


    /**
     * abstractScope constructor.
     * bind the Request
     * @param object $request
     */
    public function __construct(object $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return object|\Yonna\IO\Request
     */
    protected function request()
    {
        return $this->request;
    }

    /**
     * @param null $key
     * @return mixed
     */
    protected function input($key = null)
    {
        $input = $this->request()->getInput();
        if (empty($key)) {
            return $input;
        }
        return $input[$key] ?? null;
    }

}