package pub_sub

import (
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Producer = &cobra.Command{
	Use:   "pub_sub_producer",
	Short: "Publish message to message queue.",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp message server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("pub_sub", amqp.ExchangeFanout, false, true, false, false, nil)
		utils.LogError(err, "Can not declare a exchange.")

		err = ch.Publish("pub_sub", "", false, false, amqp.Publishing{Body: []byte("This message should broadcasting.")})
		utils.LogError(err, "Can not publish message to queue")
	},
}
