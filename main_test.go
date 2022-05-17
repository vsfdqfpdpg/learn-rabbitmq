package main

import (
	"math/rand"
	"testing"
	"time"
)

func TestRand(t *testing.T) {
	rs := rand.NewSource(time.Now().Unix())
	ra := rand.New(rs)
	println(ra.Intn(4-1) + 1)
}
