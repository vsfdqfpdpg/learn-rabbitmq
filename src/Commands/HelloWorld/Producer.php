<?php

namespace Happy\Commands\HelloWorld;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "helloworld:producer";

    public function configure()
    {
        $this->setDescription("Produce task.");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $connect = $this->getConnection();
        $channel = $connect->channel();

        $channel->queue_declare("letterbox");

        $msg = new AMQPMessage("Hello world!");

        $channel->basic_publish($msg, "", "letterbox");

        echo " [x] Sent 'Hello World!'\n";

        $channel->close();

        $connect->close();

        return Command::SUCCESS;
    }
}