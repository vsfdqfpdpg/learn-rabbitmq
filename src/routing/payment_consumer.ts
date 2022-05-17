import {connect} from "amqplib";

const payment_consumer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel()
    await channel.assertExchange("routing", "direct", {autoDelete: true})
    await channel.assertQueue("payment", {autoDelete: true})
    await channel.bindQueue("payment", "routing", "payment_only")
    await channel.consume("payment", message => {
        console.log(message?.content.toString());
    });

}

export default payment_consumer;