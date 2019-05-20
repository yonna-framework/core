<?php
/**
 * IO
 */

namespace PhpureCore;

use Elasticsearch\ClientBuilder;

class IO
{

    public static function response(object $request)
    {
        $client = ClientBuilder::create()->build();
        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];
        $response = $client->index($params);
        dump($response);
        //
        dump($request);
    }

}