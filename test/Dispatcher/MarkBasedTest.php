<?php

namespace CrazyGoat\Router\Dispatcher;

class MarkBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return new \CrazyGoat\Router\Dispatcher\MarkBased();
    }

    protected function getDataGeneratorClass()
    {
        return new \CrazyGoat\Router\DataGenerator\MarkBased();
    }
}
