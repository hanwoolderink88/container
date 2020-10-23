<?php

namespace HanWoolderink88\Container\Model;

use HanWoolderink88\Container\Traits\AliasFinder;
use ReflectionException;

class ServiceInfo
{
    use AliasFinder;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string[]
     */
    private array $aliases;

    /**
     * @var object|null
     */
    private ?object $service = null;

    /**
     * @var mixed[]|null
     */
    private ?array $constructorParams = null;

    /**
     * @var int
     */
    private int $priority;

    /**
     * ServiceInfo constructor.
     * @param string $name
     * @param string[]|null $aliases
     * @param int $priority
     * @throws ReflectionException
     */
    public function __construct(string $name, ?array $aliases = null, int $priority = 9999)
    {
        $this->name = $name;
        $this->priority = $priority;

        if ($aliases !== null) {
            $this->aliases = $aliases;
        } else {
            $this->aliases = $this->reflectAliases($name);
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

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     * @return ServiceInfo
     */
    public function setPriority(int $priority): ServiceInfo
    {
        $this->priority = $priority;

        return $this;
    }
}
