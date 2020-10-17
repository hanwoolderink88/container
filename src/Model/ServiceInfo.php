<?php

namespace HanWoolderink88\Container\Model;

use HanWoolderink88\Container\AliasFinder;
use ReflectionException;

class ServiceInfo
{
    private string $name;

    /**
     * @var string[]
     */
    private array $aliases;

    private ?object $service = null;

    /**
     * @var mixed[]|null
     */
    private ?array $constructorParams = null;

    /**
     * ServiceInfo constructor.
     * @param string $name
     * @param string[]|null $aliases
     * @throws ReflectionException
     */
    public function __construct(string $name, ?array $aliases = null)
    {
        $this->name = $name;

        if ($aliases !== null) {
            $this->aliases = $aliases;
        } else {
            $reflect = new AliasFinder();
            $this->aliases = $reflect->reflectAliases($name);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ServiceInfo
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param string[] $aliases
     * @return $this
     */
    public function setAliases(array $aliases): ServiceInfo
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function getService(): ?object
    {
        return $this->service;
    }

    public function setService(?object $service): ServiceInfo
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getConstructorParams(): array
    {
        return $this->constructorParams;
    }

    /**
     * @param mixed[] $constructorParams
     * @return $this
     */
    public function setConstructorParams(array $constructorParams): ServiceInfo
    {
        $this->constructorParams = $constructorParams;

        return $this;
    }
}
