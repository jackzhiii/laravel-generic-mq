<?php

namespace Dhf\Mq;

use Dhf\Mq\Contracts\Mq as MqInterface;
use RdKafka\Conf;
use RdKafka\Topic;
use RdKafka\Producer;

public class KafkaMq implements MqInterface
{
    protected $conf;

    public function __construct(Conf $conf)
    {
        $this->conf = $conf;
    }

    public function push(string $topic, string $message, int $delay = 0)
    {
        $producer = new RdKafka\Producer($this->conf);
        $producerTopic = $producer->newTopic($topic);

        // RD_KAFKA_PARTITION_UA 表示自动分区
        $producerTopic->produce(RD_KAFKA_PARTITION_UA, 0, $message);

        // 轮询产生的事件
        // https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.poll.html
        $producer->poll(0);
    }


    /**
     * @return String
     * @throw \Exception 
     */
    public function pop(string $topic)
    {
        $consumer = new RdKafka\KafkaConsumer($this->conf);
        $consumer->subscribe(array ($topic));

        while (true) {
            // return RdKafka\Message
            $rdKafkaMessage = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    return $rdKafkaMessage->payload;
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    throw new \Exception("No more messages;")
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    throw new \Exception("Timed out\n");
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
}
