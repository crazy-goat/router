<?hh

namespace CrazyGoat\Router\TestFixtures;

function no_options_simple(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\simpleDispatcher($collector ==> {});
}

function no_options_cached(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\cachedDispatcher($collector ==> {});
}
