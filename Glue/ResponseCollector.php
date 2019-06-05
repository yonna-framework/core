<?php

namespace PhpureCore\Glue;

use PhpureCore\Core\Glue;

/**
 * Class Response
 *
 * @method static getResponseDataType() @return
 * @method static self setResponseDataType(string $response_data_type)
 * @method static getCode()
 * @method static self setCode(int $code)
 * @method static getMessage()
 * @method static self setMessage(string $message)
 * @method static etData()
 * @method static self setData(array $data)
 * @method static getExtra(): array
 * @method static self setExtra(array $extra)
 * @method static toArray()
 * @method static toJson()
 * @method static toXml()
 * @method static response()
 * @method static end()
 *
 * @see \PhpureCore\IO\ResponseCollector
 */
class ResponseCollector extends Glue
{


}