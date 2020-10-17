<?php

namespace HanWoolderink88\Container\TestClasses;

class FooBar1 extends Bar3 implements Foo1, Foo2
{
    private string $name;

    private string $surname;

    public function __construct(string $name, string $surname)
    {
        $this->name = $name;
        $this->surname = $surname;
    }

    public function sayHi(): string
    {
        return "Hello {$this->name} {$this->surname}";
    }
}
