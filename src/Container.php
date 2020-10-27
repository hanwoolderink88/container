<?php

namespace HanWoolderink88\Container;

use Exception;
use HanWoolderink88\Container\Exception\ContainerAddServiceException;
use HanWoolderink88\Container\Exception\ContainerCannotWireException;
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
     * @var ContainerIndex
     */
    private ContainerIndex $index;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->index = new ContainerIndex();
    }

    /**
     * @param object $service
     * @param string[]|null $aliases
     * @param bool $updateIndex
     * @throws ContainerAddServiceException
     */
    public function addService(object $service, ?array $aliases = null, bool $updateIndex = false): void
    {
        $name = get_class($service);

        try {
            $serviceInfo = new ServiceInfo($name, $aliases);
        } catch (Exception $e) {
            throw new ContainerAddServiceException('Cannot add service with name ' . $name, 500);
        }

        $serviceInfo->setService($service);

        $this->services[] = $serviceInfo;

        $pos = count($this->services) - 1;
        $this->index->addItem($serviceInfo, $pos, $updateIndex, true);
    }

    /**
     * @param string $name
     * @param mixed[] $constructorParams
     * @param string[]|null $aliases
     * @param bool $updateIndex
     * @throws ContainerAddServiceException
     */
    public function addServiceReference(
        string $name,
        array $constructorParams = [],
        ?array $aliases = null,
        bool $updateIndex = false
    ): void {
        try {
            $serviceInfo = new ServiceInfo($name, $aliases);
        } catch (Exception $e) {
            throw new ContainerAddServiceException("Cannot add service with name {$name}", 500);
        }

        $serviceInfo->setConstructorParams($constructorParams);

        $this->services[] = $serviceInfo;

        $pos = count($this->services) - 1;
        $this->index->addItem($serviceInfo, $pos, $updateIndex, false);
    }

    /**
     * @param string $id name or alias of the service
     * @return mixed
     * @noinspection PhpMissingParamTypeInspection
     * @throws ReflectionException|ContainerCannotWireException
     */
    public function get($id)
    {
        $key = $this->index->find($id);
        $item = $key !== null ? $this->services[$key] : null;
        if ($item === null) {
            return null;
        }

        if ($item->getService() === null) {
            $constructorParams = $this->getConstructorParams($item->getName());
            $orderedParams = [];
            $fixedParams = $item->getConstructorParams();

            // we have DI and Registered Params
            foreach ($constructorParams as $constructorParam) {
                $name = $constructorParam['name'];
                $type = $constructorParam['type'];
                $isNullable = $constructorParam['nullable'];
                $isFixedParam = isset($fixedParams[$name]);

                $value = null;
                if ($isFixedParam) {
                    $value = $fixedParams[$name] ?? null;
                } elseif ($this->has($type)) {
                    $value = $this->get($type);
                }

                if ($value === null && $isNullable === false) {
                    $msg = "Callback function has argument \"{$type} \${$name}\" but no param or DI service was found";
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
    public function has($id): bool
    {
        return $this->index->find($id) !== null;
    }

    /**
     * @return $this
     */
    public function sortIndex():self
    {
        $this->index->sortIndex();

        return $this;
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
