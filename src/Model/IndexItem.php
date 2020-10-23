<?php

namespace HanWoolderink88\Container\Model;

class IndexItem
{
    /**
     * @var string
     */
    private string $serviceName;

    /**
     * @var string
     */
    private string $key;

    /**
     * The position the serviceInfo is in
     * @var int
     */
    private int $position;

    /**
     * Is it only a class reference (true) or does the position hold an object (false)
     * @var bool
     */
    private bool $reference;

    /**
     * @var int
     */
    private int $priority;

    /**
     * @param string $serviceName
     * @param string $key
     * @param bool $reference
     * @param int $position
     * @param int $priority
     */
    public function __construct(string $serviceName, string $key, bool $reference, int $position, int $priority)
    {
        $this->serviceName = $serviceName;
        $this->key = $key;
        $this->reference = $reference;
        $this->position = $position;
        $this->priority = $priority;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     * @return $this
     */
    public function setServiceName(string $serviceName): IndexItem
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): IndexItem
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition(int $position): IndexItem
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReference(): bool
    {
        return $this->reference;
    }

    /**
     * @param bool $reference
     * @return $this
     */
    public function setReference(bool $reference): IndexItem
    {
        $this->reference = $reference;

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
     * @return IndexItem
     */
    public function setPriority(int $priority): IndexItem
    {
        $this->priority = $priority;

        return $this;
    }
}
