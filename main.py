import click
from dotenv import load_dotenv

from hello_world.producer import hello_world
from pub_sub.second_consumer import pub_sub
from routing.analytic_consumer import routing
from rpc.client import rpc
from topic.user_consumer import topic
from work_queue.second_consumer import work_queue

load_dotenv()


@click.group()
def cli():
    pass


cli.add_command(hello_world)
cli.add_command(work_queue)
cli.add_command(pub_sub)
cli.add_command(routing)
cli.add_command(topic)
cli.add_command(rpc)

if "__main__" == __name__:
    cli()
