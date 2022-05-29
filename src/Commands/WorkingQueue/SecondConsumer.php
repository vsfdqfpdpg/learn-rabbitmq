<?php

namespace Happy\Commands\WorkingQueue;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SecondConsumer extends BasicCommand
{
    protected static $defaultName = "workingqueue:second-consumer";

    protected function configure()
    {
        $this->setDescription("Competitive second consumer.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->queue_declare("letterbox");

        $channel->basic_qos(null, 1, null);

        $channel->basic_consume("letterbox", "", false, false, false, false, function (AMQPMessage $message) {
            $consume_time = mt_rand(2, 9);
            echo "Second consumer: " . $message->getBody() . " need {$consume_time} second to consume this task." . PHP_EOL;
            sleep($consume_time);
            $message->ack();
        });

        while ($channel->is_open()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}