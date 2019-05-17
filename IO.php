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
        dd($this->request);

        return $this;
    }

    protected function crypto()
    {
        if (!$this->crypto && !empty($this->request->cargo->config)) {
            $this->crypto = new Crypto(CONFIG['crypto']);
        }
        return $this->crypto;
    }

    /**
     * 返回结果
     * @param $array
     * @return string
     */
    private function result($array)
    {
        $array['stack'] = $this->stack;
        $result = json_encode($array);
        if ($this->is_crypto === true) {
            $result = $this->crypto()->encrypt($result);
        }
        return $result;
    }

}