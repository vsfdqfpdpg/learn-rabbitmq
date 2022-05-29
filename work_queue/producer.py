import random
import time

import click

from config.connection import connect


@click.group()
def work_queue():
    """Working queue pattern"""
    pass


@work_queue.command("producer")
def producer():
    conn = connect()
    ch = conn.channel()
    queue = ch.queue_declare("work_queue", auto_delete=True)

    count = 0

    while True:
        sl = random.randint(1, 3)
        body = " %d : This message should be broadcasting." % count
        print(body)
        ch.basic_publish("", queue.method.queue, body=body.encode("utf8"))
        time.sleep(sl)
        count += 1
