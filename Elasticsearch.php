<?php


namespace PhpureCore;

use Elasticsearch\ClientBuilder;

class Elasticsearch
{

    public function __construct()
    {
        $elasticsearch_hosts = [
            [
                'host' => '127.0.0.1',
                'port' => '9200',
                'scheme' => 'http',
            ],
            /*
            [
                'host' => 'foo.com',
                'port' => '9200',
                'scheme' => 'https',
                'user' => 'username',
                'pass' => 'password!#$?*abc'
            ],
            */
        ];
        $client = ClientBuilder::create()
            ->setHosts($elasticsearch_hosts)
            ->setRetries(10)
            ->build();
        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];
        $response = $client->index($params);
        $a = $client->get([
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id'
        ]);
        dump($response);
        var_dump($a);
    }

}