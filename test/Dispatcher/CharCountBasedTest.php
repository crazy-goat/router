<?php

namespace CrazyGoat\Router\Dispatcher;

class CharCountBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return new \CrazyGoat\Router\Dispatcher\CharCountBased();
    }

    protected function getDataGeneratorClass()
    {
        return new \CrazyGoat\Router\DataGenerator\CharCountBased();
    }
}
