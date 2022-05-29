<?php

namespace Happy\Commands\Topic;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PurchaseConsumer extends BasicCommand
{
    protected static $defaultName = "topic:purchase";

    protected function configure()
    {
        $this->setDescription("Topic purchase consumer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::TOPIC);
        [$queue] = $channel->queue_declare("", false, false, true);
        $channel->queue_bind($queue, "letterbox", "#.purchase.#");
        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Topic purchase consumer: " . $message->getBody() . PHP_EOL;
        });
        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}