<?php
/**
 * Bootstrap handle collector
 */

namespace PhpureCore\Handle;


/**
 * Class HandleCollector
 * @package PhpureCore\Handle
 */
class HandleCollector
{

    private $response_data_type = 'json';

    private $code = 0;
    private $message = '';
    private $data = array();
    private $extra = array();

    /**
     * @return string
     */
    public function getResponseDataType(): string
    {
        return $this->response_data_type;
    }

    /**
     * @param string $response_data_type
     * @return HandleCollector
     */
    public function setResponseDataType(string $response_data_type): self
    {
        $this->response_data_type = $response_data_type;
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
     * @return HandleCollector
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return HandleCollector
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
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
     * @return HandleCollector
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
     * @return HandleCollector
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
            'message' => $this->getMessage(),
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
        return json_encode($this->toArray());
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
            default:
                $response = $this->toJson();
                break;
        }
        return $response;
    }

}