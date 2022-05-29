import uuid

from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import Basic, BasicProperties

from config.connection import connect
from .server import rpc


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body: bytes):
    print(f"{properties.correlation_id}: {body}")
    channel.basic_ack(delivery_tag=method.delivery_tag)


@rpc.command("client")
def client():
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("", exclusive=True, auto_delete=True)
    request_queue = ch.queue_declare("request-queue", auto_delete=True)
    unique_id = uuid.uuid4()
    message = f"{unique_id} Can i get some data?"

    ch.basic_publish("",
                     request_queue.method.queue,
                     message.encode("utf8"),
                     BasicProperties(
                         reply_to=queue.method.queue,
                         correlation_id=str(unique_id)
                     ))
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
