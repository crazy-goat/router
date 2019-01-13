<?php

namespace CrazyGoat\Router\Dispatcher;

class GroupCountBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return 'CrazyGoat\\Router\\Dispatcher\\GroupCountBased';
    }

    protected function getDataGeneratorClass()
    {
        return 'CrazyGoat\\Router\\DataGenerator\\GroupCountBased';
    }
}
