import {connect} from "amqplib"

const producer = async () => {
    let connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    })
    let channel = await connection.createChannel();
    await channel.assertQueue("letterbox", {durable: false, autoDelete: true});
    let count = 0;
    while (true) {
        let duration = Math.floor(Math.random() * (3 - 1) + 1)
        await new Promise(resolve => setTimeout(resolve, duration * 1000));
        channel.publish("", "letterbox", Buffer.from(`${count}: This message should be broadcasting.`));
        count++;
    }
}

export default producer;