package cmd

import (
	"log"

	"rabbitmq/cmd/hello_world"
	"rabbitmq/cmd/pub_sub"
	"rabbitmq/cmd/routing"
	"rabbitmq/cmd/rpc"
	"rabbitmq/cmd/topic"
	"rabbitmq/cmd/work_queue"

	"github.com/spf13/cobra"
)

var rootCmd = &cobra.Command{
	Use: "",
}

func init() {
	rootCmd.AddCommand(hello_world.Producer)
	rootCmd.AddCommand(hello_world.Consumer)

	rootCmd.AddCommand(pub_sub.Producer)
	rootCmd.AddCommand(pub_sub.FirstConsumer)
	rootCmd.AddCommand(pub_sub.SecondConsumer)

	rootCmd.AddCommand(work_queue.Producer)
	rootCmd.AddCommand(work_queue.FirstCosumer)
	rootCmd.AddCommand(work_queue.SecondCosumer)

	rootCmd.AddCommand(routing.Producer)
	rootCmd.AddCommand(routing.AnalyticsRouting)
	rootCmd.AddCommand(routing.PaymentRouting)

	rootCmd.AddCommand(topic.Producer)
	rootCmd.AddCommand(topic.AnalyticConsumer)
	rootCmd.AddCommand(topic.PaymentConsumer)
	rootCmd.AddCommand(topic.UserConsumer)

	rootCmd.AddCommand(rpc.Server)
	rootCmd.AddCommand(rpc.Client)
}

func Execute() {
	if err := rootCmd.Execute(); err != nil {
		log.Fatalln(err.Error())
	}
}
