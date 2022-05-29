from config.connection import connect
from .consumer import hello_world


@hello_world.command("producer")
def produce():
    """Produce message to hello world message queue."""
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("messagebox", auto_delete=False, durable=True)
    ch.basic_publish("", queue.method.queue, b'hello world')

    ch.close()
    conn.close()
