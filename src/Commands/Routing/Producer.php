<?php

namespace Happy\Commands\Routing;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "routing:producer";

    protected function configure()
    {
        $this->setDescription("Routing pattern: analytics consumer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare("letterbox", AMQPExchangeType::DIRECT);

        $message = new AMQPMessage("Send message to purchase.");
        $channel->basic_publish($message, "letterbox", "purchase");

        $message = new AMQPMessage("Send message to analytics.");
        $channel->basic_publish($message, "letterbox", "analytics");

        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}