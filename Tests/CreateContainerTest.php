<?php

namespace HanWoolderink88\Container\Tests;

use HanWoolderink88\Container\Container;
use HanWoolderink88\Container\ContainerAddServiceException;
use HanWoolderink88\Container\TestClasses\FooBar1;
use PHPUnit\Framework\TestCase;

class CreateContainerTest extends TestCase
{
    /**
     * @throws ContainerAddServiceException
     */
    public function testAddService(): void
    {
        $service = new FooBar1('Han', 'Woolderink');

        $container = new Container();
        $container->addService($service);
        $container->buildIndex();

        $found = $container->get(FooBar1::class);

        $this->assertTrue(get_class($found) === FooBar1::class, 'Class is not the same');
    }

    /**
     * @throws ContainerAddServiceException
     */
    public function testAddServiceReference(): void
    {
        $name = FooBar1::class;
        $params = ['name' => 'Han', 'surname' => 'Woolderink'];
        $container = new Container();
        $container->addServiceReference($name, $params);
        $container->buildIndex();

        $found = $container->get($name);

        $this->assertEquals('Hello Han Woolderink', $found->sayHi(), 'not the same');
    }
}
