import click
from pika.exchange_type import ExchangeType

from config.connection import connect


@click.group()
def pub_sub():
    """Publish subscription pattern"""
    pass


@pub_sub.command("producer")
def producer():
    conn = connect()
    channel = conn.channel()

    channel.exchange_declare("pub_sub", exchange_type=ExchangeType.fanout.value, auto_delete=True)
    msg = "This message should broadcasting."
    channel.basic_publish("pub_sub", "", body=msg.encode("utf8"))
    channel.close()
    conn.close()
