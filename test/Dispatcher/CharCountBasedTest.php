<?php

namespace CrazyGoat\Router\Dispatcher;

class CharCountBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return 'CrazyGoat\\Router\\Dispatcher\\CharCountBased';
    }

    protected function getDataGeneratorClass()
    {
        return 'CrazyGoat\\Router\\DataGenerator\\CharCountBased';
    }
}
