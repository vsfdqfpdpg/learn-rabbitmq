<?php

namespace Happy\Commands\HeaderExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "header-exchange:consumer";

    protected function configure()
    {
        $this->setDescription("Consumer a message based on header's setting.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare("header_exchange", AMQPExchangeType::HEADERS);
        [$queue] = $channel->queue_declare("", false, false, true);
        $channel->queue_bind($queue, "header_exchange", "", false, new AMQPTable(["x-match" => "any", "name" => "custom", "age" => 18]));
        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Header Matched: " . $message->getBody() . PHP_EOL;
        });
        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}