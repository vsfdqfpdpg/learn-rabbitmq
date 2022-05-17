import {connect} from "amqplib";
import {randomUUID} from "crypto";

const client = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel()
    const queue = await channel.assertQueue("", {autoDelete: true})
    await channel.assertQueue("request-queue", {autoDelete: true})


    channel.publish("", "request-queue", Buffer.from("send to server"), {
        replyTo: queue.queue,
        correlationId: randomUUID()
    })

    await channel.consume(queue.queue, message => {
        console.log(`${message?.properties.correlationId}: ` + message?.content.toString())
    })
}

export default client;