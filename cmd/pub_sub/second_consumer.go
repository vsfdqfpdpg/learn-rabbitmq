package pub_sub

import (
	"log"
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var SecondConsumer = &cobra.Command{
	Use:   "pub_sub_second_consumer",
	Short: "Second consumer to consume pub sub queue",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("pub_sub", amqp.ExchangeFanout, false, true, false, false, nil)
		utils.LogError(err, "Can not declare a exchange")

		queue, err := ch.QueueDeclare("", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		err = ch.QueueBind(queue.Name, "", "pub_sub", false, nil)
		utils.LogError(err, "Can not bind queue to exchange")

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consumer message from queue")

		var forever chan struct{}

		go func() {
			for message := range messages {
				log.Printf("Pub Sub Second Comsumer: %s", message.Body)
				message.Ack(false)
			}
		}()
		<-forever
	},
}
