<?php
namespace PhpureCore\Bootstrap;

abstract class AbstractClass
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

}