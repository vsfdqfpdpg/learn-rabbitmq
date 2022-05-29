import click
from pika.spec import ExchangeType

from config.connection import connect


@click.group()
def topic():
    """Topic pattern"""
    pass


@topic.command("producer")
def producer():
    conn = connect()
    ch = conn.channel()
    ch.exchange_declare("topic", ExchangeType.topic.value, auto_delete=True)

    payment_msg = "Someone bought butter."
    ch.basic_publish("topic", "user.payment", payment_msg.encode("utf8"))

    analytic_msg = "This message should be analysis."
    ch.basic_publish("topic", "message.analytic.topic", analytic_msg.encode("utf8"))

    user_msg = "analyze this user."
    ch.basic_publish("topic", "user.analytic.topic", user_msg.encode("utf8"))
    ch.close()
    conn.close()
