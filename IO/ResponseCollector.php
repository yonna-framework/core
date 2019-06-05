<?php
/**
 * Bootstrap handle collector
 */

namespace PhpureCore\IO;


use Convert;

/**
 * Class Collector
 * @package PhpureCore\IO
 */
class ResponseCollector
{

    private $response_data_type = 'json';
    private $charset = 'utf-8';
    private $code = 0;
    private $msg = '';
    private $data = array();
    private $extra = array();

    public function __construct()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseDataType(): string
    {
        return $this->response_data_type;
    }

    /**
     * @param string $response_data_type
     * @return ResponseCollector
     */
    public function setResponseDataType(string $response_data_type): self
    {
        $this->response_data_type = $response_data_type;
        return $this;
    }

    /**
     * @return string
     */
    public function getCharset(): string
    {
        return $this->charset;
    }

    /**
     * @param string $charset
     * @return ResponseCollector
     */
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @param int $code
     * @return ResponseCollector
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     * @return ResponseCollector
     */
    public function setMsg(string $msg): self
    {
        $this->msg = $msg;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return ResponseCollector
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return ResponseCollector
     */
    public function setExtra(array $extra): self
    {
        $this->extra = $extra;
        return $this;
    }

    /**
     * to array
     * @return array
     */
    public function toArray()
    {
        $data = $this->getData();
        if ($this->getExtra()) {
            foreach ($this->getExtra() as $k => $v) {
                $data[$k] = $v;
            }
        }
        return array(
            'code' => $this->getCode(),
            'msg' => $this->getMsg(),
            'data' => $data,
        );
    }

    /**
     * to JSON
     * @return false|string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * to JSON
     * @return false|string
     */
    public function toXml()
    {
        return xmlrpc_encode(Convert::obj2String($this->toArray()));
    }

    /**
     * to Text
     * @return false|string
     */
    public function toHtml()
    {
        return Convert::arr2html(Convert::obj2String($this->toArray()));
    }

    /**
     * to Text
     * @return false|string
     */
    public function toText()
    {
        return var_export($this->toArray(), true);
    }

    /**
     * to JSON
     * @return false|string
     */
    public function response()
    {
        $response = null;
        switch ($this->getResponseDataType()) {
            case 'xml':
                $response = $this->toXml();
                break;
            case 'json':
                $response = $this->toJson();
                break;
            case 'html':
                $response = $this->toHtml();
                break;
            case 'text':
            default:
                $response = $this->toText();
                break;
        }
        return $response;
    }

    /**
     * @return string
     */
    public function end()
    {
        switch ($this->getResponseDataType()) {
            case 'xml':
                header('Content-Type:application/xml; charset=' . $this->getCharset());
                break;
            case 'json':
                header('Content-Type:application/json; charset=' . $this->getCharset());
                break;
            case 'html':
                header('Content-Type:text/html; charset=' . $this->getCharset());
                break;
            default:
                header('Content-Type:text/plain; charset=' . $this->getCharset());
                break;
        }
        exit($this->response());
    }

}