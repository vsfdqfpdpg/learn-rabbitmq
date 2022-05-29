<?php

namespace Happy\Commands\AlternateExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "alternate-exchange:consumer";

    protected function configure()
    {
        $this->setDescription("Setup multiple queues for alternate exchange and main exchange respectively.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare("alternate_exchange", AMQPExchangeType::FANOUT);
        $channel->exchange_declare("main_exchange",
            AMQPExchangeType::DIRECT,
            false,
            false,
            true,
            false,
            false,
            new AMQPTable(["alternate-exchange" => "alternate_exchange"])
        );

        $channel->queue_declare("test");
        $channel->queue_bind("test", "main_exchange", "test");
        $channel->basic_consume("test", "", false, false, false, false, function (AMQPMessage $message) {
            echo "Main exchange message received: " . $message->getBody() . PHP_EOL;
        });

        [$queue] = $channel->queue_declare("");
        $channel->queue_bind($queue, "alternate_exchange");
        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo 'Alternate exchange message received: ' . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}