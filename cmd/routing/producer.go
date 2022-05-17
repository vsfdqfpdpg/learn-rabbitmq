package routing

import (
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Producer = &cobra.Command{
	Use:   "routing_producer",
	Short: "Routing producer",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("routing", amqp.ExchangeDirect, false, false, false, false, nil)
		utils.LogError(err, "Can not declare a exchange")

		err = ch.Publish("routing", "analytics_only", false, false, amqp.Publishing{Body: []byte("Data should be analysed.")})
		utils.LogError(err, "Can not publish message to channel")

		err = ch.Publish("routing", "payment_only", false, false, amqp.Publishing{Body: []byte("Buy some butter.")})
		utils.LogError(err, "Can not publish message to channel")
	},
}
