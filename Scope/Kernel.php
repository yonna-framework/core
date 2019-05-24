<?php

namespace PhpureCore\Scope;

use PhpureCore\Core;

/**
 * Class Kernel
 * @package PhpureCore\Scope
 */
abstract class Kernel implements \PhpureCore\Scope\Interfaces\Kernel
{

    /**
     * @var \PhpureCore\IO\Request $request
     */
    private $request = null;

    /**
     * @var \PhpureCore\Database\Coupling $db
     */
    private $db = null;

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
     * @return object|\PhpureCore\IO\Request
     */
    protected function request()
    {
        return $this->request;
    }

    /**
     * @return \PhpureCore\IO\Input
     */
    protected function input()
    {
        return $this->request()->input;
    }

    /**
     * @return object|\PhpureCore\Database\Coupling
     */
    protected function db()
    {
        if (!$this->db) {
            $this->db = Core::singleton(\PhpureCore\Database\Coupling::class, $this->request()->cargo->config['database']);
        }
        return $this->db;
    }


}