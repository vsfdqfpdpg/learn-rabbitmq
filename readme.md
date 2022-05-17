### Rabbitmq examples
[Rabbitmq](https://github.com/rabbitmq/rabbitmq-tutorials/tree/master/go)

### golang

```bash
go mod init rabbitmq
```

### install dependences
```bash
go get -u github.com/spf13/cobra@latest
go get -u github.com/spf13/viper
go get -u github.com/streadway/amqp
go get -u github.com/google/uuid
```

### rabbitmq delete an exchange
```bash
sudo rabbitmqadmin delete exchange --vhost=/ name='exchange001'
```

### rabbitmq delete a queue
```bash
sudo rabbitmqadmin delete queue name=queue_name
```