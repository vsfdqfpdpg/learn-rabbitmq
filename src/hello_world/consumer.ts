import {connect} from "amqplib"

const hello_world_consumer = async () => {
    let connection = await connect({
        hostname: process.env.AMQP_HOSTNAME,
        port: parseInt(process.env.AMQP_PORT as string) || 5672,
        username: process.env.AMQP_USERNAME,
        password: process.env.AMQP_PASSWORD
    })
    let channel = await connection.createChannel()
    await channel.assertQueue("letterbox", {durable: false, autoDelete: true})
    await channel.consume("letterbox", message => {
        console.log(`Hello world consumer: ${message?.content}`)
    }, {noAck: false})
}
export default hello_world_consumer