import click
from pika.spec import ExchangeType

from config.connection import connect


@click.group()
def routing():
    """Routing pattern"""
    pass


@routing.command("producer")
def producer():
    conn = connect()
    ch = conn.channel()
    ch.exchange_declare("routing", ExchangeType.direct.value, auto_delete=True)

    payment_msg = "Someone bought butter."
    ch.basic_publish("routing", "payment_only", payment_msg.encode("utf8"))

    analytic_msg = "This message should be analysis."

    ch.basic_publish("routing", "analytic_only", analytic_msg.encode("utf8"))
    ch.close()
    conn.close()
