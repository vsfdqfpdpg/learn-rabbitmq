package main

import (
	"rabbitmq/cmd"
	"rabbitmq/utils"
)

func main() {
	utils.LoadEnv()
	cmd.Execute()
}
