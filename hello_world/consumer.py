import click
from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import Basic

from config.connection import connect


@click.group()
@click.pass_context
def hello_world(ctx):
    """Hello world pattern."""
    pass


def callback(channel: BlockingChannel, method: Basic.Deliver, properties, body):
    print(f"hellow world consumer: %s" % body)
    channel.basic_ack(delivery_tag=method.delivery_tag)


@hello_world.command("consumer")
def consume():
    """Consumer hello world message."""
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("messagebox", auto_delete=False, durable=True)
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
