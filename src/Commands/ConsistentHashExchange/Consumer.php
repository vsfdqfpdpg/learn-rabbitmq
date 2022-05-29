<?php

namespace Happy\Commands\ConsistentHashExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "consistent-hash-exchange:consumer";

    protected function configure()
    {
        $this->setDescription("Consume message by its hashed key.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("simple-hashing", "x-consistent-hash");

        [$queue_1] = $channel->queue_declare("");
        // routing key will be the percentage of total messages will be consumed.
        $channel->queue_bind($queue_1, "simple-hashing", "1");
        $channel->basic_consume($queue_1, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Hashed queue 1: " . $message->getBody() . PHP_EOL;
        });

        [$queue_2] = $channel->queue_declare("");
        $channel->queue_bind($queue_2, "simple-hashing", "4");
        $channel->basic_consume($queue_2, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Hashed queue 2: " . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}