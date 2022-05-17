package routing

import (
	"log"
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var AnalyticsRouting = &cobra.Command{
	Use:   "routing_analytics",
	Short: "Comsume message from a routing channel",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("routing", amqp.ExchangeDirect, false, false, false, false, nil)
		utils.LogError(err, "Can not declare an exchagne")

		queue, err := ch.QueueDeclare("analytics", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		err = ch.QueueBind(queue.Name, "analytics_only", "routing", false, nil)
		utils.LogError(err, "Can not bind queue to an exchange")

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume messages")

		forver := make(chan struct{})

		go func() {
			for message := range messages {
				log.Printf("Analytics consumer: %s\n", message.Body)
				message.Ack(false)
			}
		}()
		<-forver
	},
}
