import {connect} from "amqplib";

const server = async () => {
    const connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });

    const channel = await connection.createChannel()
    await channel.assertQueue("request-queue", {autoDelete: true})

    await channel.consume("request-queue", async message => {
        channel.sendToQueue(message?.properties.replyTo, Buffer.from("Back to client"), {correlationId: message?.properties.correlationId})
        await channel.ack(message!)
    }, {noAck: false})

}

export default server;