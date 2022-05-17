package rpc

import (
	"log"
	"rabbitmq/utils"

	"github.com/google/uuid"
	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Client = &cobra.Command{
	Use:   "rpc_client",
	Short: "rpc client",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		queue, err := ch.QueueDeclare("", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		request_queue, err := ch.QueueDeclare("request-queue", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		CorrelationId := uuid.New().String()
		err = ch.Publish("", request_queue.Name, false, false, amqp.Publishing{Body: []byte(CorrelationId + ": Send to server"), CorrelationId: CorrelationId, ReplyTo: queue.Name})
		utils.LogError(err, "Can not publsih message to queue")

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume message from queue")

		forever := make(chan struct{})
		go func() {
			for message := range messages {
				log.Printf("Client consume: %s: %s\n", message.CorrelationId, message.Body)
				message.Ack(false)
			}
		}()
		<-forever

	},
}
