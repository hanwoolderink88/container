<?php

namespace HanWoolderink88\Container;

use ReflectionClass;
use ReflectionException;

class AliasFinder
{
    /**
     * @param string $name
     * @return string[]
     * @throws ReflectionException
     */
    public function reflectAliases(string $name): array
    {
        $function = new ReflectionClass($name);
        $interfaces = $function->getInterfaceNames();
        $parentClasses = $this->getParentClasses($function);

        return array_merge($interfaces, $parentClasses);
    }

    /**
     * @param ReflectionClass $reflection
     * @param string[] $returnArray
     * @return string[]
     */
    private function getParentClasses(ReflectionClass $reflection, array $returnArray = []): array
    {
        $parent = $reflection->getParentClass();
        if ($parent !== false) {
            $nest = $this->getParentClasses($parent, $returnArray);
            $returnArray = [...$nest, $parent->name];
        }

        return $returnArray;
    }
}
