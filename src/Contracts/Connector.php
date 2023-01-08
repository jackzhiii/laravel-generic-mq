<?php

namespace Dhf\Mq\Contracts;

interface Connector
{
    /**
     * 连接服务
     */
    public function connect(array $config);
}
