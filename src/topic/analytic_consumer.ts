import {connect} from "amqplib";

const analytic_consumer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel()
    await channel.assertExchange("topic", "topic", {autoDelete: true})
    const queue = await channel.assertQueue("", {autoDelete: true})
    await channel.bindQueue(queue.queue, "topic", "*.analytic.*")
    await channel.consume(queue.queue, message => {
        console.log(message?.content.toString())
    })
}

export default analytic_consumer