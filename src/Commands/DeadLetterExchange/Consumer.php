<?php

namespace Happy\Commands\DeadLetterExchange;

use Happy\Commands\BasicCommand;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Consumer extends BasicCommand
{
    protected static $defaultName = "dead-letter-exchange:consumer";

    protected function configure()
    {
        $this->setDescription("Dead letter exchange will consume expired message.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $this->getConnection();
        $channel = $connection->channel();

        $channel->exchange_declare("dead_letter_exchange", AMQPExchangeType::FANOUT);
        [$queue] = $channel->queue_declare("");
        $channel->queue_bind($queue, "dead_letter_exchange");

        $channel->exchange_declare("main_exchange", AMQPExchangeType::DIRECT);
        $channel->queue_declare("test",
            false,
            false,
            false,
            true,
            false,
            new AMQPTable(["x-dead-letter-exchange" => "dead_letter_exchange", "x-message-ttl" => 2000])
        );

        $channel->queue_bind("test", "main_exchange", "test");


        $channel->basic_consume($queue, "", false, false, false, false, function (AMQPMessage $message) {
            echo "Dead letter exchange: " . $message->getBody() . PHP_EOL;
        });

        while ($channel->is_open()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
        return Command::SUCCESS;
    }
}