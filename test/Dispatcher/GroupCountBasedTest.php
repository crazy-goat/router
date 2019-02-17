<?php

namespace CrazyGoat\Router\Dispatcher;

class GroupCountBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return new \CrazyGoat\Router\Dispatcher\GroupCountBased();
    }

    protected function getDataGeneratorClass()
    {
        return new \CrazyGoat\Router\DataGenerator\GroupCountBased();
    }
}
