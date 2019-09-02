<?php
/**
 * Request
 */

namespace Yonna\IO;

use Yonna\Bootstrap\Cargo;

/**
 * Class Request
 * @package Core\Core\IO
 */
class Request extends RequestBuilder
{

    /**
     * Request constructor.
     * @param Cargo $cargo
     * @param RequestBuilder|null $requestBuilder
     */
    public function __construct(Cargo $cargo, RequestBuilder $requestBuilder = null)
    {
        // load cargo
        $this->cargo = $cargo;
        // load global
        $this->loadGlobal();
        // load builder
        if ($requestBuilder != null && $requestBuilder instanceof RequestBuilder) {
            $this->loadRequestBuilder($requestBuilder);
        }
        return Crypto::input($this);
    }

}