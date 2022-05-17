import {connect} from "amqplib";

const first_consumer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel();
    await channel.assertExchange("pubsub", "fanout");
    const queue = await channel.assertQueue("", {autoDelete: true})
    await channel.bindQueue(queue.queue, "pubsub", "")

    await channel.consume(queue.queue, message => {
        console.log(message?.content.toString())
    }, {noAck: false})

}

export default first_consumer;