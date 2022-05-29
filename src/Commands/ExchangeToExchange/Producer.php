<?php

namespace Happy\Commands\ExchangeToExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "exchange-to-exchange:producer";

    protected function configure()
    {
        $this->setDescription("Publish message gone through multiple exchange.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare("first_exchange", AMQPExchangeType::DIRECT);
        $channel->exchange_declare("second_exchange", AMQPExchangeType::FANOUT);

        $channel->exchange_bind("second_exchange", "first_exchange");

        $message = new AMQPMessage("This message has gone through multiple exchanges.");
        $channel->basic_publish($message, "first_exchange");

        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}