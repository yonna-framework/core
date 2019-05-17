<?php
/**
 * IO
 */

namespace PhpureCore;

use PhpureCore\IO\Crypto;
use PhpureCore\IO\Request;

class IO
{

    /**
     * @var Request|null 
     */
    private $request = null;
    private $stack = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * 执行并返回结果
     * @param $array
     * @return string
     */
    public function exec($array)
    {
        $array['stack'] = $this->stack;
        $result = json_encode($array);
        if ($this->is_crypto === true) {
            $result = $this->crypto()->encrypt($result);
        }
        return $result;
    }

}