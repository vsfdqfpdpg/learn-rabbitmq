<?php

namespace Happy\Commands\PubSub;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "pubsub:producer";

    protected function configure()
    {
        $this->setDescription("Pub sub producer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::FANOUT);
        $message = new AMQPMessage("This message should publish to exchange.");
        $channel->basic_publish($message, "letterbox");
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}