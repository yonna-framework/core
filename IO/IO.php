<?php
/**
 * input / output
 */

namespace PhpureCore\IO;

class IO
{

    private $request = null;

    public function __construct()
    {
        return $this;
    }

    public function response(object $request)
    {
        $this->request = $request;
        dump($this->request);
    }

}