<?php

namespace HanWoolderink88\Container;

use Exception;
use HanWoolderink88\Container\Model\IndexItem;
use HanWoolderink88\Container\Model\ServiceInfo;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    /**
     * @var ServiceInfo[]
     */
    private array $services = [];

    /**
     * @var IndexItem[]
     */
    private array $index = [];

    /**
     * @param object $service
     * @param string[]|null $aliases
     * @throws ContainerAddServiceException
     */
    public function addService(object $service, ?array $aliases = null): void
    {
        $name = get_class($service);
        try {
            $serviceInfo = new ServiceInfo($name, $aliases);
        } catch (Exception $e) {
            throw new ContainerAddServiceException('Cannot add service with name ' . $name, 500);
        }

        $serviceInfo->setService($service);
        $this->services[] = $serviceInfo;
    }

    /**
     * @param string $name
     * @param mixed[] $constructorParams
     * @param string[]|null $aliases
     * @throws ContainerAddServiceException
     */
    public function addServiceReference(string $name, array $constructorParams = [], ?array $aliases = null): void
    {
        try {
            $serviceInfo = new ServiceInfo($name, $aliases);
        } catch (Exception $e) {
            throw new ContainerAddServiceException("Cannot add service with name {$name}", 500);
        }

        $serviceInfo->setConstructorParams($constructorParams);

        $this->services[] = $serviceInfo;
    }

    public function buildIndex(): void
    {
        $this->index = [];

        foreach ($this->services as $pos => $service) {
            $name = $service->getName();
            $this->index[] = new IndexItem($name, $name, $pos);
            foreach ($service->getAliases() as $alias) {
                $this->index[] = new IndexItem($name, $alias, $pos);
            }
        }
    }

    /**
     * @param string $id name or alias of the service
     * @return mixed
     * @noinspection PhpMissingParamTypeInspection
     */
    public function get($id)
    {
        $item = $this->find($id);
        if ($item === null) {
            return null;
        }

        if ($item->getService() === null) {
            $reflection = new ReflectionClass($item->getName());
            $constructor = $reflection->getConstructor();
            $paramOrder = $constructor->getParameters();

            $params = $item->getConstructorParams();
            $orderedParams = [];
            foreach ($paramOrder as $paramI) {
                $name = $paramI->getName();
                if (isset($params[$name])) {
                    $orderedParams[] = $params[$name];
                } else {
                    $orderedParams[] = null;
                }
            }

            $className = $item->getName();

            return new $className(...$orderedParams);
        }

        return $item->getService();
    }

    /**
     * @param string $id name or alias of the service
     * @return bool
     * @noinspection PhpMissingParamTypeInspection
     */
    public function has($id)
    {
        return (bool)$this->find($id);
    }

    private function find(string $id): ?ServiceInfo
    {
        foreach ($this->index as $item) {
            if ($item->getKey() === $id) {
                return $this->services[$item->getPosition()];
            }
        }

        return null;
    }
}
