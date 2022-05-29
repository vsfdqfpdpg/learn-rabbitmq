<?php

namespace Happy\Common;

use PhpAmqpLib\Connection\AMQPStreamConnection;

trait AmqpTrait
{
    public function getConnection() : AMQPStreamConnection{
        return new AMQPStreamConnection(getenv("AMQP_HOST"), getenv("AMQP_PORT"), getenv("AMQP_USER"), getenv("AMQP_PASSWORD"));
    }
}