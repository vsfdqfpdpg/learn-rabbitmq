import {Command} from 'commander';

import HelloWorldConsumer from "./src/hello_world/consumer"
import HelloWorldProducer from "./src/hello_world/producer"
import WorkQueueSecondConsumer from "./src/work_queue/second_consumer"
import WorkQueueFirstConsumer from "./src/work_queue/first_consumer"
import WorkQueueProducer from "./src/work_queue/producer"

import PubSubProducer from "./src/pub_sub/producer"
import PubSubFirstConsumer from "./src/pub_sub/first_consumer"
import PubSubSecondConsumer from "./src/pub_sub/second_consumer"

import RoutingProducer from "./src/routing/producer"
import RoutingAnalytic from "./src/routing/analytic_consumer"
import RoutingPayment from "./src/routing/payment_consumer"

import TopicProducer from "./src/topic/producer"
import TopicAnalyticConsumer from "./src/topic/analytic_consumer"
import TopicUserConsumer from "./src/topic/user_consumer"
import TopicPaymentConsumer from "./src/topic/payment_consumer"

import RpcServer from "./src/rpc/server"
import RpcClient from "./src/rpc/client"

import dotenv from "dotenv"

dotenv.config()

const program = new Command()

program.command("hello-world-consumer").action(async () => {
    await HelloWorldConsumer()
});

program.command("hello-world-producer").action(async () => {
    await HelloWorldProducer()
});

program.command("work-queue-first-consumer").action(async () => {
    await WorkQueueFirstConsumer()
});

program.command("work-queue-second-consumer").action(async () => {
    await WorkQueueSecondConsumer()
});

program.command("work-queue-producer").action(async () => {
    await WorkQueueProducer()
});

program.command("pub-sub-producer").action(async () => {
    await PubSubProducer()
});

program.command("pub-sub-first-consumer").action(async () => {
    await PubSubFirstConsumer()
});

program.command("pub-sub-second-consumer").action(async () => {
    await PubSubSecondConsumer()
});

program.command("routing-producer").action(async () => {
    await RoutingProducer()
});

program.command("routing-analytic-consumer").action(async () => {
    await RoutingAnalytic()
})

program.command("routing-payment-consumer").action(async () => {
    await RoutingPayment()
})

program.command("topic-producer").action(async () => {
    await TopicProducer()
})

program.command("topic-analytic-consumer").action(async () => {
    await TopicAnalyticConsumer()
})

program.command("topic-payment-consumer").action(async () => {
    await TopicPaymentConsumer()
})

program.command("topic-user-consumer").action(async () => {
    await TopicUserConsumer()
})

program.command("rpc-server").action(async () => {
    await RpcServer()
})

program.command("rpc-client").action(async () => {
    await RpcClient()
})

program.parse();