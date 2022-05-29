<?php

namespace Happy\Commands\RPC;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Client extends BasicCommand
{
    protected static $defaultName = "rpc:client";

    protected function configure()
    {
        $this->setDescription("RPC client");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->queue_declare("letterbox");

        [$queue] = $channel->queue_declare("", false, false, true);
        $id = uniqid();
        $message = new AMQPMessage("$id: Can i request for some data?", ["reply_to" => $queue, "correlation_id" => $id]);
        $channel->basic_publish($message, "", "letterbox");

        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Client data received: " . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}