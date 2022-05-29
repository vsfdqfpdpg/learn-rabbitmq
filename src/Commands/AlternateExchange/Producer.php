<?php

namespace Happy\Commands\AlternateExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "alternate-exchange:producer";

    protected function configure()
    {
        $this->setDescription("Bind a alternate exchange to main exchange, when message is not routable to main exchange, it will go to alternate exchange.");
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
        $message = new AMQPMessage("This message should go to main exchange and test queue.");
        $channel->basic_publish($message, "main_exchange", "test");
        $message = new AMQPMessage("This message should go to alternate exchange and its bind queue.");
        $channel->basic_publish($message, "main_exchange", "not-routable");
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}