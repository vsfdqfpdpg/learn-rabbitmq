import random
import time

from pika.adapters.blocking_connection import BlockingChannel
from pika.spec import Basic, BasicProperties

from config.connection import connect
from .producer import work_queue


def callback(channel: BlockingChannel, method: Basic.Deliver, properties: BasicProperties, body):
    sl = random.randint(2, 5)
    print("Working queue first consumer: " + str(body) + f"need %s seconds to consume." % sl)
    time.sleep(sl)
    channel.basic_ack(delivery_tag=method.delivery_tag)


@work_queue.command("first_consumer")
def first_consumer():
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("work_queue", auto_delete=True)
    ch.basic_qos(prefetch_count=1)
    ch.basic_consume(queue.method.queue, on_message_callback=callback, auto_ack=False)
    ch.start_consuming()
