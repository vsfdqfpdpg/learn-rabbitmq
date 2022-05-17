package topic

import (
	"log"
	"rabbitmq/utils"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var UserConsumer = &cobra.Command{
	Use:   "topic_user_consumer",
	Short: "consume message from an exchange",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		err = ch.ExchangeDeclare("topic", amqp.ExchangeTopic, false, true, false, false, nil)
		utils.LogError(err, "Can not declare an exchange")

		queue, err := ch.QueueDeclare("", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue.")

		err = ch.QueueBind(queue.Name, "user.#", "topic", false, nil)
		utils.LogError(err, "Can not bind queue to an exchange")

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume a message")

		forever := make(chan struct{})

		go func() {
			for message := range messages {
				log.Printf("User consumer: %s\n", message.Body)
				message.Ack(false)
			}
		}()

		<-forever
	},
}
