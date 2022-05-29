<?php

namespace Happy\Commands\WorkingQueue;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Producer extends BasicCommand
{
    protected static $defaultName = "workingqueue:producer";

    protected function configure()
    {
        $this->setDescription("Competitive working queue.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connect = $this->getConnection();
        $channel = $connect->channel();

        $channel->queue_declare("letterbox");

        $count = 0;

        while (true) {
            $sleep_time = mt_rand(1, 3);
            $message = new AMQPMessage("Id: $count This message should be broadcast.");
            $channel->basic_publish($message, "", "letterbox");
            $count++;
            sleep($sleep_time);
        }
    }

}