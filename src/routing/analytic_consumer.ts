import {connect} from "amqplib";

const analytic_consumer = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });
    const channel = await connection.createChannel()
    await channel.assertExchange("routing", "direct", {autoDelete: true});
    await channel.assertQueue("analytics", {autoDelete: true})
    await channel.bindQueue("analytics", "routing", "analytics_only")
    await channel.consume("analytics", message => {
        console.log(message?.content.toString())
    }, {noAck: false})
}

export default analytic_consumer;