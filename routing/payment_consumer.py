from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import ExchangeType, BasicProperties, Basic

from config.connection import connect
from .producer import routing


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body: bytes):
    print(body)
    channel.basic_ack(delivery_tag=method.delivery_tag)


@routing.command("payment-consumer")
def payment_consumer():
    conn = connect()
    ch = conn.channel()
    ch.exchange_declare("routing", ExchangeType.direct.value, auto_delete=True)
    queue = ch.queue_declare("", exclusive=True, auto_delete=True)
    ch.queue_bind(queue.method.queue, "routing", "payment_only")
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
