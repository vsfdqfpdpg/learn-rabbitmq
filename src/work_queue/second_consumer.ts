import {connect} from "amqplib"

const second_consumer = async () => {
    let connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    })
    let channel = await connection.createChannel();
    await channel.assertQueue("letterbox", {durable: false, autoDelete: true});
    await channel.prefetch(1);
    await channel.consume("letterbox", async message => {
        let time_to_consumer = Math.floor(Math.random() * (9 - 3) + 3)
        console.log("Working queue second consumer: " + message?.content + ` need ${time_to_consumer} seconds to consume.`);
        await new Promise((resolve, reject) => setTimeout(resolve, time_to_consumer * 1000))
        channel.ack(message!)
    }, {noAck: false});

}

export default second_consumer