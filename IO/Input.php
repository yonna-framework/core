<?php

namespace PhpureCore\IO;

/**
 * Class Input
 * @package PhpureCore\IO
 */
class Input
{

    public $data = array();
    public $file = null;
    public $stack = '';

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getStack(): string
    {
        return $this->stack;
    }

    /**
     * @param string $stack
     */
    public function setStack(string $stack): void
    {
        $this->stack = $stack;
    }

}