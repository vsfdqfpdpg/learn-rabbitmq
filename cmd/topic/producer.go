package topic

import (
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Producer = &cobra.Command{
	Use:   "topic_producer",
	Short: "produce a message to channel",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("topic", amqp.ExchangeTopic, false, true, false, false, nil)
		utils.LogError(err, "Can not declare an exchange")

		err = ch.Publish("topic", "user.login.in", false, false, amqp.Publishing{Body: []byte("Someone has logged in.")})
		utils.LogError(err, "Can not publish message to exchange")

		err = ch.Publish("topic", "europe.analytic.sold", false, false, amqp.Publishing{Body: []byte("Someone bought butter.")})
		utils.LogError(err, "Can not publish message to exchange")

		err = ch.Publish("topic", "user.payment.make", false, false, amqp.Publishing{Body: []byte("Someone make an order.")})
		utils.LogError(err, "Can not publish message to exchange")
	},
}
