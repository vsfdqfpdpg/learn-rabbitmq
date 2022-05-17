import {connect} from "amqplib"

const producer = async () => {
    let connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    });
    let channel = await connection.createChannel();
    await channel.assertQueue("letterbox", {durable: false, autoDelete: true});
    channel.publish("", "letterbox", Buffer.from("Hello world!"));
    await channel.close();
    await connection.close();
}

export default producer