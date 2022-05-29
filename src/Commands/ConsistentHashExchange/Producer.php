<?php

namespace Happy\Commands\ConsistentHashExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "consistent-hash-exchange:producer";

    protected function configure()
    {
        $this->setDescription("Hashing routing key and publish this message.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("simple-hashing", "x-consistent-hash");

        while (true) {
            $duration = mt_rand(1, 3);
            $id = uniqid();
            $message = new AMQPMessage("$id : This message is sending by hashed exchanged.");
            $channel->basic_publish($message, "simple-hashing", $id);
            sleep($duration);
        }
    }

}