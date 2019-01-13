<?php

namespace CrazyGoat\Router\Dispatcher;

class GroupPosBasedTest extends DispatcherTest
{
    protected function getDispatcherClass()
    {
        return 'CrazyGoat\\Router\\Dispatcher\\GroupPosBased';
    }

    protected function getDataGeneratorClass()
    {
        return 'CrazyGoat\\Router\\DataGenerator\\GroupPosBased';
    }
}
