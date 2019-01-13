<?hh

namespace CrazyGoat\Router\TestFixtures;

function empty_options_simple(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\simpleDispatcher($collector ==> {}, shape());
}

function empty_options_cached(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\cachedDispatcher($collector ==> {}, shape());
}
