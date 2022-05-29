<?php

namespace Happy\Commands\HelloWorld;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "helloworld:consumer";

    protected function configure()
    {
        $this->setDescription("Consume task.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $connect = $this->getConnection();
        $channel = $connect->channel();

        $channel->queue_declare("letterbox");

        echo " [*] Waiting for messages. To exit press CTRL+C\n";

        $channel->basic_consume("letterbox", "", false, false, false, false, function (AMQPMessage $msg) {
            echo $msg->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connect->close();
        return Command::SUCCESS;
    }
}