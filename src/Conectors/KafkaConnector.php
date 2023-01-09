<?php

namespace Dhf\Mq\Conectors;

use Dhf\Mq\Contracts\Connector as ConnectorInterface;
use RdKafka\Conf;
use InvalidArgumentException;
use Dhf\Mq\KafkaMq;

class KafkaConnector implements ConnectorInterface
{
    /**
     * @return \Dhf\Mq\Contracts\Mq;
     */
    public function connect(array $config)
    {
        return new KafkaMq($this->initConf($config));
    }

    /**
     * 初始化 Kafka Client 配置
     */
    protected function initConf(array $config)
    {
        $conf = new Conf();

        if (! isset($config['metadata.broker.list'])) {
            throw new InvalidArgumentException("Kafka config require metadata.broker.list");
        }

        // 配置属性
        collect($config)->except(['driver'])->each(function($attrValue, $attrKey) use ($conf) {
            $conf->set((string) $attrKey, $attrValue);
        });

        return $conf;
    }
}
