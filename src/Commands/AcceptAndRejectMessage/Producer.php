<?php

namespace Happy\Commands\AcceptAndRejectMessage;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "accept-reject-message:producer";

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::FANOUT);
        $message = new AMQPMessage("This message should be broadcasting.");
        $channel->basic_publish($message, "letterbox", "test");
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}