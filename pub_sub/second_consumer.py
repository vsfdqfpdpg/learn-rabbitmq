from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import ExchangeType, BasicProperties, Basic

from config.connection import connect
from .first_consumer import pub_sub


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body: bytes):
    print(f"Second consumer: {body}")
    channel.basic_ack(delivery_tag=method.delivery_tag)


@pub_sub.command("second_consumer")
def second_consumer():
    conn = connect()
    ch = conn.channel()
    ch.exchange_declare("pub_sub", ExchangeType.fanout.value, auto_delete=True)
    queue = ch.queue_declare("", exclusive=True, auto_delete=True)
    ch.queue_bind(queue.method.queue, "pub_sub")

    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
