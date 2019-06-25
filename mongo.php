$manager = new \MongoDB\Driver\Manager("mongodb://ppm_moner:GvaR2qfBiX@10.53.4.5:27001/ppm");
        $bulk = new \MongoDB\Driver\BulkWrite();
        $bulk->insert(array(
            'product_id'        => 123,
            'product_name'      => 'zyzyzy',
            'product_price'     => 2139.00,
        ));
        try {
            $result = $manager->executeBulkWrite('ppm.test', $bulk);
            var_dump($result->getInsertedCount());
        } catch (\MongoDB\Driver\Exception\BulkWriteException $e) {
        }

        $query = new \MongoDB\Driver\Query([]);
        $cursor = $manager->executeQuery('ppm.test', $query);
        foreach ($cursor as $document) {
            print_r($document);
        }