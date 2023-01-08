<?php

return [
    'default' => env('MQ_CONNECTION', 'redis'),

    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => env('QUEUE_REDIS_CONNECTION', 'default'),
            'queue' => env('QUEUE_NAME','default'),
            'retry_after' => 300,
            'block_for' => null,
        ],

        'kafka' => [
            'driver' => 'kafka',
            'metadata.broker.list' => 'localhost:9092',
            'group.id' => 'myConsumerGroup',
            'auto.offset.reset' => true,
            'enable.partition.eof' => true,
        ]
    ],
];
