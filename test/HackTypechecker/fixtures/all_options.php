<?hh

namespace CrazyGoat\Router\TestFixtures;

function all_options_simple(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\simpleDispatcher(
      $collector ==> {},
      shape(
        'routeParser' => \CrazyGoat\Router\RouteParser\Std::class,
        'dataGenerator' => \CrazyGoat\Router\DataGenerator\GroupCountBased::class,
        'dispatcher' => \CrazyGoat\Router\Dispatcher\GroupCountBased::class,
        'routeCollector' => \CrazyGoat\Router\RouteCollector::class,
      ),
    );
}

function all_options_cached(): \CrazyGoat\Router\Dispatcher {
    return \CrazyGoat\Router\cachedDispatcher(
      $collector ==> {},
      shape(
        'routeParser' => \CrazyGoat\Router\RouteParser\Std::class,
        'dataGenerator' => \CrazyGoat\Router\DataGenerator\GroupCountBased::class,
        'dispatcher' => \CrazyGoat\Router\Dispatcher\GroupCountBased::class,
        'routeCollector' => \CrazyGoat\Router\RouteCollector::class,
        'cacheFile' => '/dev/null',
        'cacheDisabled' => false,
      ),
    );
}
