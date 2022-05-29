import os

from pika import ConnectionParameters, PlainCredentials, BlockingConnection


def connect() -> BlockingConnection:
    credential = PlainCredentials(
        username=os.getenv("RABBITMQ_USER"),
        password=os.getenv("RABBITMQ_PASSWORD")
    )

    parameter = ConnectionParameters(
        host=os.getenv("RABBITMQ_HOST"),
        port=os.getenv("RABBITMQ_PORT"),
        credentials=credential
    )

    return BlockingConnection(parameter)
