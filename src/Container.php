<?php

namespace HanWoolderink88\Container;

use Exception;
use HanWoolderink88\Container\Model\IndexItem;
use HanWoolderink88\Container\Model\ServiceInfo;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

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
     * @throws ReflectionException|ContainerCannotWireException
     */
    public function get($id)
    {
        $item = $this->find($id);
        if ($item === null) {
            return null;
        }

        if ($item->getService() === null) {
            $constructorParams = $this->getConstructorParams($item->getName());
            $orderedParams = [];
            $fixedParams = $item->getConstructorParams();

            // we have DI and Registered Params
            foreach ($constructorParams as $constructorParam) {
                $isFixedParam = isset($fixedParams[$constructorParam['name']]);
                $isNullable = $constructorParam['nullable'];
                $value = null;

                if ($isFixedParam) {
                    // a wildcard param is defined in the route path by /{name}
                    $value = $fixedParams[$constructorParam['name']] ?? null;
                } elseif ($this->has($constructorParam['type'])) {
                    $value = $this->get($constructorParam['type']);
                }

                if ($value === null && $isNullable === false) {
                    $name = $constructorParam['name'];
                    $msg = "Callback function has argument with name \"{$name}\" but no param or DI service was found";
                    throw new ContainerCannotWireException($msg);
                }

                $orderedParams[] = $value;
            }

            $name = $item->getName();

            return new $name(...$orderedParams);
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

    /**
     * @param string $objectName
     * @return mixed[][]
     * @throws ReflectionException
     */
    private function getConstructorParams(string $objectName): array
    {
        $reflection = new ReflectionClass($objectName);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return [];
        }

        $params = [];
        foreach ($constructor->getParameters() as $fParam) {
            $params[] = [
                'name' => $fParam->getName(),
                /** @phpstan-ignore-next-line */
                'type' => $fParam->getType() ? $fParam->getType()->getName() : null,
                'nullable' => $fParam->allowsNull()
            ];
        }

        return $params;
    }
}
