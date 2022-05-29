<?php

namespace Happy\Commands;

use Happy\Common\AmqpTrait;
use Symfony\Component\Console\Command\Command;

class BasicCommand extends Command
{
    use AmqpTrait;
}