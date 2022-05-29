<?php

namespace Happy\Commands\ExchangeToExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "exchange-to-exchange:consumer";

    protected function configure()
    {
        $this->setDescription("Consume message from queue");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("second_exchange", AMQPExchangeType::FANOUT);
        $channel->queue_declare("letterbox");
        $channel->queue_bind("letterbox", "second_exchange");
        $channel->basic_consume("letterbox", "", false, false, false, false, function (AMQPMessage $message) {
            echo "Exchange to exchange: " . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}