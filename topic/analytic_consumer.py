from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import ExchangeType, BasicProperties, Basic

from config.connection import connect
from .producer import topic


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body: bytes):
    print(body)
    channel.basic_ack(delivery_tag=method.delivery_tag)


@topic.command("analytic_consumer")
def analytic_consumer():
    conn = connect()
    ch = conn.channel()
    ch.exchange_declare("topic", ExchangeType.topic.value, auto_delete=True)
    queue = ch.queue_declare("", auto_delete=True, exclusive=True)
    ch.queue_bind(queue.method.queue, "topic", "*.analytic.*")
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
