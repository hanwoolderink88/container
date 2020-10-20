<?php

namespace HanWoolderink88\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class ContainerCannotWireException extends Exception implements NotFoundExceptionInterface
{
}
