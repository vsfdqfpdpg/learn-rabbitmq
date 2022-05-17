package rpc

import (
	"log"
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Server = &cobra.Command{
	Use:   "rpc_server",
	Short: "Rpc server",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		queue, err := ch.QueueDeclare("request-queue", false, true, false, false, nil)
		utils.LogError(err, "Can not create a queue")

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume a mesage")

		forever := make(chan struct{})
		go func() {
			for message := range messages {
				log.Printf("Sever consume: %s\n", message.Body)
				err = ch.Publish("", message.ReplyTo, false, false, amqp.Publishing{Body: []byte("Back to client."), CorrelationId: message.CorrelationId})
				utils.LogError(err, "Can not publish message back to queue")
				message.Ack(false)
			}
		}()
		<-forever

	},
}
