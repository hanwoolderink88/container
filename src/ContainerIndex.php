<?php

namespace HanWoolderink88\Container;

use HanWoolderink88\Container\Model\IndexItem;
use HanWoolderink88\Container\Model\ServiceInfo;

class ContainerIndex
{
    /**
     * @var IndexItem[]
     */
    private array $index;

    /**
     * ContainerIndex constructor.
     * @param IndexItem[]|null $index
     */
    public function __construct(array $index = null)
    {
        $this->index = $index ?? [];
    }

    /**
     * @param ServiceInfo $service
     * @param int $pos
     * @param bool $sort
     * @return ContainerIndex
     */
    public function addItem(ServiceInfo $service, int $pos, bool $sort = false): self
    {
        $name = $service->getName();
        $isReference = $service->getService() === null;

        // add the definitive class
        $this->index[] = new IndexItem($name, $name, $isReference, $pos, $service->getPriority());

        // add all polymorphic references
        foreach ($service->getAliases() as $alias) {
            $this->index[] = new IndexItem($name, $alias, $isReference, $pos, $service->getPriority());
        }

        if ($sort === true) {
            $this->sortIndex();
        }

        return $this;
    }

    /**
     * @param string $id
     * @return int|null
     */
    public function find(string $id): ?int
    {
        foreach ($this->index as $item) {
            if ($item->getKey() === $id) {
                return $item->getPosition();
            }
        }

        return null;
    }

    /**
     * The index is being used this::get() and this::has().
     * A search query can have multiple answers where logic dictates which of the found element is the right answer
     * To optimize for speed we always want to return the first element. for this reason we presort the index array and
     * do a linear search.
     *
     * The sorting can be done manually or by inserting a second param in this::addToIndex()
     *
     * @return ContainerIndex the sorted index
     */
    public function sortIndex(): self
    {
        $withObject = [];
        $withObjectAlias = [];

        $withoutObject = [];
        $withoutObjectAlias = [];
        foreach ($this->index as $index) {
            if ($index->isReference()) {
                if ($index->getServiceName() === $index->getKey()) {
                    $withoutObject[] = $index;
                } else {
                    $withoutObjectAlias[] = $index;
                }
            } elseif ($index->getServiceName() === $index->getKey()) {
                $withObject[] = $index;
            } else {
                $withObjectAlias[] = $index;
            }
        }

        usort($withObject, [$this, 'sortByPriority']);
        usort($withObjectAlias, [$this, 'sortByPriority']);
        usort($withoutObject, [$this, 'sortByPriority']);
        usort($withoutObjectAlias, [$this, 'sortByPriority']);

        $this->index = [
            ...$withObject,
            ...$withObjectAlias,
            ...$withoutObject,
            ...$withoutObjectAlias,
        ];

        return $this;
    }

    /**
     * @param IndexItem $a
     * @param IndexItem $b
     * @return int
     */
    private function sortByPriority(IndexItem $a, IndexItem $b): int
    {
        return strcmp((string)$a->getPriority(), (string)$b->getPriority());
    }
}
