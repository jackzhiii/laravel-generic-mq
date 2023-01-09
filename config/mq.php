<?php

return [
    'default' => env('MQ_CONNECTION', 'kafka'),

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
            'metadata.broker.list' => '127.0.0.1:9092',
            'group.id' => 'myConsumerGroup',
            'auto.offset.reset' => 'earliest',
            'enable.partition.eof' => true,
        ]
    ],
];
