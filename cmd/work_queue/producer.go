package work_queue

import (
	"rabbitmq/utils"
	"strconv"
	"time"

	"github.com/spf13/cobra"
	"github.com/streadway/amqp"
)

var Producer = &cobra.Command{
	Use:   "work_queue_producer",
	Short: "Produce message to work queue",
	Run: func(cmd *cobra.Command, args []string) {
		conn, err := amqp.Dial(utils.Env.AMQP_URL)
		utils.LogError(err, "Can not connect to amqp server")
		defer conn.Close()

		ch, err := conn.Channel()
		utils.LogError(err, "Can not create a channel")
		defer ch.Close()

		queue, err := ch.QueueDeclare("work_queue", false, true, false, false, nil)
		utils.LogError(err, "Can not declare a work queue")

		count := 0

		for {
			err = ch.Publish("", queue.Name, false, false, amqp.Publishing{Body: []byte(strconv.Itoa(count) + ": This message should consumed by consumer")})
			utils.LogError(err, "Can not publish a message to work queue")
			time.Sleep(time.Duration(time.Second * time.Duration(utils.RandInt(4, 1))))
			count++
		}
	},
}
