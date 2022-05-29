import click
from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import Basic, BasicProperties

from config.connection import connect


@click.group()
def rpc():
    """Rpc pattern"""
    pass


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body: bytes):
    print(f"Server: {body}")
    message = f"{properties.correlation_id} : This is the message you wanted."
    channel.basic_publish("",
                          properties.reply_to,
                          message.encode("utf8"),
                          BasicProperties(
                              correlation_id=properties.correlation_id
                          ))
    channel.basic_ack(delivery_tag=method.delivery_tag)


@rpc.command("server")
def server():
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("request-queue", auto_delete=True)
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
