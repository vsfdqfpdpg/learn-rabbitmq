import { connect } from "amqplib";

const producer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel();
    await channel.assertExchange("routing", "direct", { autoDelete: false, durable: false })
    channel.publish("routing", "analytics_only", Buffer.from("Data should be analysed."))
    channel.publish("routing", "payment_only", Buffer.from('Buy some butter'));
    await channel.close()
    await connection.close()
}

export default producer;