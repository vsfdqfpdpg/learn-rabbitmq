package hello_world

import (
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Producer = &cobra.Command{
	Use:   "hello_world_producer",
	Short: "Hello world sender",
	Long:  "Send to",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp host")
		defer conn.Close()

		channel, err := conn.Channel()
		utils.LogError(err, "Can not crate a channel")
		defer channel.Close()

		queue, err := channel.QueueDeclare("messagebox", true, false, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		err = channel.Publish("", queue.Name, false, false, amqp.Publishing{Body: []byte("Hello world!")})
		utils.LogError(err, "Can not publish message to queue")
	},
}
