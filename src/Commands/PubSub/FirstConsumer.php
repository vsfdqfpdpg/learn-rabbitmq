<?php

namespace Happy\Commands\PubSub;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FirstConsumer extends BasicCommand
{
    protected static $defaultName = "pubsub:first-consumer";

    protected function configure()
    {
        $this->setDescription("Pub sub first consumer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::FANOUT);
        [$queue] = $channel->queue_declare("", false, false, true);

        $channel->queue_bind($queue, "letterbox");

        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Pub sub first consumer: " . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        return Command::SUCCESS;
    }

}