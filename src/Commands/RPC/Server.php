<?php

namespace Happy\Commands\RPC;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends BasicCommand
{
    protected static $defaultName = "rpc:server";

    protected function configure()
    {
        $this->setDescription("RPC server");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->queue_declare("letterbox");

        $channel->basic_consume("letterbox", "", false, false, false, false, function (AMQPMessage $message) {
            echo "Server received data: " . $message->getBody() . PHP_EOL;
            $msg = new AMQPMessage($message->get('correlation_id') . ": This is the data you need.");
            $message->getChannel()->basic_publish($msg, "", $message->get("reply_to"));
            $message->ack();
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        return Command::SUCCESS;
    }

}