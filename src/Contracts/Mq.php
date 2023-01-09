<?php

namespace Dhf\Mq\Contracts;

interface Mq
{
    public function push(string $topic, string $message);

    public function pop(string $topic);
}
