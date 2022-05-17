package work_queue

import (
	"log"
	"rabbitmq/utils"
	"time"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var SecondCosumer = &cobra.Command{
	Use:   "work_queue_second_consumer",
	Short: "Second consumer for work queue",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		queue, err := ch.QueueDeclare("work_queue", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a queue")

		ch.Qos(1, 0, false)

		messages, err := ch.Consume(queue.Name, "", false, false, false, false, nil)
		utils.LogError(err, "Can not consume a message")

		forever := make(chan struct{})

		go func() {
			for message := range messages {
				log.Printf("Work queue second consumer: %s\n", message.Body)
				time.Sleep(time.Second * time.Duration(utils.RandInt(9, 5)))
				message.Ack(false)
			}
		}()

		<-forever
	},
}
