<?php

namespace Happy\Commands\AcceptAndRejectMessage;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "accept-reject-message:consumer";

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();
        $channel->exchange_declare("letterbox", AMQPExchangeType::FANOUT);
        [$queue] = $channel->queue_declare("test");
        $channel->queue_bind($queue, "letterbox");
        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo $message->getBody() . PHP_EOL;
            if ($message->getDeliveryTag() % 5) {
                $message->ack(true);
            }
        });
        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}