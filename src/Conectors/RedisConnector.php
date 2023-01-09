<?php

namespace Dhf\Mq\Conectors;

use Illuminate\Contracts\Redis\Factory as Redis;
use Dhf\Mq\RedisMq;
use Dhf\Mq\Contracts\Connector as ConnectorInterface;

class RedisConnector implements ConnectorInterface
{
    protected $redis;

    protected $connection;

    public function __construct(Redis $redis, $connection = null)
    {
        $this->redis = $redis;
        $this->connection = $connection;
    }

    public function connect(array $config)
    {
        return new RedisMq(
            $this->redis, $config['queue'],
            $config['connection'] ?? $this->connection,
            $config['retry_after'] ?? 60,
            $config['block_for'] ?? null
        );
    }
}
