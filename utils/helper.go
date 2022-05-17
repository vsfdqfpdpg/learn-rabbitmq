package utils

import (
	"log"
	"math/rand"
	"time"

	"github.com/spf13/viper"
)

func LogError(err error, msg string) {
	if err != nil {
		log.Fatalln(msg + ": " + err.Error())
	}
}

func RandInt(max, min int) int {
	rs := rand.NewSource(time.Now().Unix())
	ra := rand.New(rs)
	return ra.Intn(max-min) + min
}

type EnvConfigs struct {
	AMQP_URL string `mapstructure:"AMQP_URL"`
}

var Env *EnvConfigs

func LoadEnv() {
	viper.AddConfigPath(".")
	viper.SetConfigName(".env")
	viper.SetConfigType("env")
	if err := viper.ReadInConfig(); err != nil {
		log.Fatal("Can not load configure file. ", err.Error())
	}

	if err := viper.Unmarshal(&Env); err != nil {
		log.Fatal("Can not unmarshal env to struct. ", err.Error())
	}
}
