<?php

use Happy\Commands\HelloWorld\Consumer;
use Happy\Commands\HelloWorld\Producer;
use Happy\Commands\Routing\AnalyticsConsumer;
use Happy\Commands\Routing\PurchaseConsumer;
use Happy\Commands\RPC\Client;
use Happy\Commands\RPC\Server;
use Happy\Commands\Topic\TopicProducer;
use Happy\Commands\Topic\UserConsumer;
use Happy\Commands\WorkingQueue\FirstConsumer;
use Happy\Commands\WorkingQueue\SecondConsumer;
use Symfony\Component\Console\Application;

require_once __DIR__ . "/bootstrap.php";

$app = new Application();

$app->add(new Producer());
$app->add(new Consumer());

$app->add(new \Happy\Commands\WorkingQueue\Producer());
$app->add(new FirstConsumer());
$app->add(new SecondConsumer());

$app->add(new \Happy\Commands\PubSub\Producer());
$app->add(new \Happy\Commands\PubSub\FirstConsumer());
$app->add(new \Happy\Commands\PubSub\SecondConsumer());

$app->add(new \Happy\Commands\Routing\Producer());
$app->add(new AnalyticsConsumer());
$app->add(new PurchaseConsumer());

$app->add(new TopicProducer());
$app->add(new \Happy\Commands\Topic\AnalyticsConsumer());
$app->add(new \Happy\Commands\Topic\PurchaseConsumer());
$app->add(new UserConsumer());

$app->add(new Server());
$app->add(new Client());

$app->add(new \Happy\Commands\ExchangeToExchange\Consumer());
$app->add(new \Happy\Commands\ExchangeToExchange\Producer());

$app->add(new \Happy\Commands\HeaderExchange\Producer());
$app->add(new \Happy\Commands\HeaderExchange\Consumer());

$app->add(new \Happy\Commands\ConsistentHashExchange\Consumer());
$app->add(new \Happy\Commands\ConsistentHashExchange\Producer());

$app->add(new \Happy\Commands\AlternateExchange\Producer());
$app->add(new \Happy\Commands\AlternateExchange\Consumer());

$app->add(new Happy\Commands\DeadLetterExchange\Consumer());
$app->add(new Happy\Commands\DeadLetterExchange\Producer());

$app->add(new Happy\Commands\AcceptAndRejectMessage\Producer());
$app->add(new Happy\Commands\AcceptAndRejectMessage\Consumer());

$app->run();