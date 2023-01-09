<?php

namespace Dhf\Mq\Conectors;

use Dhf\Mq\Contracts\Connector as ConnectorInterface;
use RdKafka\Conf as RdKafkaConf;
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
        $conf = new RdKafkaConf();

        if (! isset($config['metadata.broker.list'])) {
            throw new InvalidArgumentException("Kafka config require metadata.broker.list");
        }

        // 配置属性
        collect($config)->except(['driver'])->each(function($attrValue, $attrKey) use ($conf) {
            $conf->set((string) $attrKey, $attrValue);
        });
// dd($conf->dump());

/*
golbal callback setting
        $conf->setDrMsgCb(function ($kafka, $message) {
            if ($message->err) {
                // message permanently failed to be delivered
                throw new \Exception("message permanently failed to be delivered");
            } else {
                // message successfully delivered
                throw new \Exception("message successfully delivered");
            }
        });

        // Set error callback
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            throw new \Exception(sprintf("Kafka error: %s (reason: %s)\n", rd_kafka_err2str($err), $reason));
        });
*/
        return $conf;
    }
}
