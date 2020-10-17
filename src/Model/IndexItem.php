<?php

namespace HanWoolderink88\Container\Model;

class IndexItem
{
    private string $serviceName;

    private string $key;

    private int $position;

    public function __construct(string $serviceName, string $key, int $position)
    {
        $this->serviceName = $serviceName;
        $this->key = $key;
        $this->position = $position;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): IndexItem
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): IndexItem
    {
        $this->key = $key;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): IndexItem
    {
        $this->position = $position;

        return $this;
    }
}
