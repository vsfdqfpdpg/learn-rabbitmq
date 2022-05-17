package hello_world

import (
	"log"
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Consumer = &cobra.Command{
	Use:   "hello_world_consumer",
	Short: "basic advence queue consume message",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect amqp server.")
		defer conn.Close()
		channel, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer channel.Close()
		queue, err := channel.QueueDeclare("messagebox", true, false, false, false, nil)
		utils.LogError(err, "Can not declare a queue")
		err = channel.Qos(1, 0, false)
		utils.LogError(err, "Can not set qos")
		messages, err := channel.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume queue message")
		var forever chan struct{}
		go func() {
			for message := range messages {
				log.Printf("%s\n", message.Body)
				message.Ack(false)
			}
		}()
		<-forever
	},
}
