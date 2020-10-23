<?php

namespace HanWoolderink88\Container\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class ContainerCannotWireException extends Exception implements NotFoundExceptionInterface
{
}
