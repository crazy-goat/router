<?php

namespace CrazyGoat\Router\Dispatcher;

class GroupPosBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return new \CrazyGoat\Router\Dispatcher\GroupPosBased();
    }

    protected function getDataGeneratorClass()
    {
        return new \CrazyGoat\Router\DataGenerator\GroupPosBased();
    }
}
