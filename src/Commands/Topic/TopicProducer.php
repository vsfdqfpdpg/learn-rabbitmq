<?php

namespace Happy\Commands\Topic;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TopicProducer extends BasicCommand
{
    protected static $defaultName = "topic:producer";

    protected function configure()
    {
        $this->setDescription("Topic producer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::TOPIC);
        $message = new AMQPMessage("Some one paid for this product.");
        $channel->basic_publish($message, "letterbox", "user.purchase.analytic");

        $message = new AMQPMessage("User has been login.");
        $channel->basic_publish($message, "letterbox", "user.login.analytic");

        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }

}