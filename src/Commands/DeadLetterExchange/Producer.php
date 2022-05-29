<?php

namespace Happy\Commands\DeadLetterExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "dead-letter-exchange:producer";

    protected function configure()
    {
        $this->setDescription("Message expired will go to dead letter exchange queue.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("main_exchange", AMQPExchangeType::DIRECT);
        $message = new AMQPMessage("This message will be expired after 2 seconds.");
        $channel->basic_publish($message, "main_exchange", "test");
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}