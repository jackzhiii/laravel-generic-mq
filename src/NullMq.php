<?php

namespace Dhf\Mq;

use Dhf\Mq\Contracts\Mq as MqInterface;

class NullMq extends Mq implements MqInterface
{
    public function push(string $topic, string $message, int $delay = 0)
    {
        // todo
    }

    public function pop(string $topic)
    {
        // todo
    }
}


