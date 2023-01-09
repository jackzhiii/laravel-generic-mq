<?php

namespace Dhf\Mq;

use Dhf\Mq\Contracts\Mq as MqInterface;
use RdKafka\Conf;
use RdKafka\Topic;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;

class KafkaMq extends BaseMq implements MqInterface
{
    protected $conf;

    protected $producer = null;

    protected $consumer = null;

    public function __construct(Conf $conf)
    {
        $this->conf = $conf;
    }

    public function push(string $topic, string $message, int $delay = 0)
    {
        $this->producer = ($producer = new Producer($this->conf));
        $producerTopic = $producer->newTopic($topic);

        // RD_KAFKA_PARTITION_UA 表示自动分区
        $producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // 轮询产生的事件
        // https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.poll.html
        $producer->poll(0);

        for ($flushRetries = 0; $flushRetries < 3; $flushRetries++) {
            $result = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }

        if (RD_KAFKA_RESP_ERR_NO_ERROR !== $result) {
            throw new \RuntimeException('Was unable to flush, messages might be lost! ' . $result);
        }
    }

    /**
     * @return String
     * @throw \Exception 
     */
    public function pop(string $topic)
    {
        $this->consumer = ($consumer = new KafkaConsumer($this->conf));
        $consumer->subscribe((array) $topic);

        // return RdKafka\Message
        $rdKafkaMessage = $consumer->consume(120*1000);
        switch ($rdKafkaMessage->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return $rdKafkaMessage->payload;
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                throw new \Exception("No more messages;");
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                throw new \Exception("Timed out\n");
                break;
            default:
                throw new \Exception($rdKafkaMessage->errstr(), $rdKafkaMessage->err);
                break;
        }
    }

    public function ack()
    {
        $this->consumer->commit();
    }

    public function getConsumer()
    {
        return $this->consumer;
    }
}
