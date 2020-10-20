<?php

namespace HanWoolderink88\Container\Tests;

use HanWoolderink88\Container\Container;
use HanWoolderink88\Container\ContainerAddServiceException;
use HanWoolderink88\Container\ContainerCannotWireException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class CreateContainerTest extends TestCase
{
    /**
     * @throws ContainerAddServiceException
     * @throws ContainerCannotWireException
     * @throws ReflectionException
     */
    public function testAddService(): void
    {
        $service = new HelloWorld();

        $container = new Container();
        $container->addService($service);
        $container->buildIndex();

        $found = $container->get(HelloWorld::class);

        $this->assertTrue(get_class($found) === HelloWorld::class, 'Class is not the same');
    }

    /**
     * @throws ContainerAddServiceException
     * @throws ContainerCannotWireException
     * @throws ReflectionException
     */
    public function testAddServiceReference(): void
    {
        $container = new Container();

        $container->addServiceReference(HelloWorld::class);
        $container->addServiceReference(FooBar1::class, ['name' => 'Han', 'surname' => 'Woolderink']);

        $container->buildIndex();

        $found = $container->get(Foo1::class);

        $this->assertEquals('Hello Han Woolderink and Hello World', $found->sayHi(), 'not the same');
    }
}

class HelloWorld
{
    public function sayHi()
    {
        return 'Hello World';
    }
}

class Bar1
{
}

abstract class Bar2 extends Bar1
{
}

class Bar3 extends Bar2
{
}

interface Foo1
{
}

interface Foo2
{
}

class FooBar1 extends Bar3 implements Foo1, Foo2
{
    private string $name;

    private string $surname;

    private HelloWorld $hw;

    public function __construct(string $name, string $surname, HelloWorld $hw)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->hw = $hw;
    }

    public function sayHi(): string
    {
        return "Hello {$this->name} {$this->surname} and {$this->hw->sayHi()}";
    }
}
