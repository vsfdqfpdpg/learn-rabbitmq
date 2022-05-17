import {connect} from "amqplib";

const producer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel()
    await channel.assertExchange("topic", "topic", {autoDelete: true})
    channel.publish("topic", "user.login.in", Buffer.from("Someone has logged in."))
    channel.publish("topic", "europe.analytic.sold", Buffer.from("Someone bought butter."))
    channel.publish("topic", "user.payment.make", Buffer.from("Someone make an order."))
    await channel.close()
    await connection.close()
}

export default producer;