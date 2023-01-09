<?php

namespace Dhf\Mq\Conectors;

use Dhf\Mq\Contracts\Connector as ConnectorInterface;
use Dhf\Mq\NullMq;

class NullConnector implements ConnectorInterface
{
    public function connect(array $config)
    {
        return new NullMq;
    }
}
