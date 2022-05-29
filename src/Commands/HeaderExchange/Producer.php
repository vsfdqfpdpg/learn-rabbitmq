<?php

namespace Happy\Commands\HeaderExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "header-exchange:producer";

    protected function configure()
    {
        $this->setDescription("Product a message with headers.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("header_exchange", AMQPExchangeType::HEADERS);
        $header = new AMQPTable(["name" => "custom"]);
        $message = new AMQPMessage("This message need published with headers");
        $message->set("application_headers", $header);
        $channel->basic_publish($message, "header_exchange");
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}