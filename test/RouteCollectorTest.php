<?php

namespace CrazyGoat\Router;

use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{
    public function testShortcuts()
    {
        $r = new DummyRouteCollector();

        $r->delete('/delete', 'delete');
        $r->get('/get', 'get');
        $r->head('/head', 'head');
        $r->patch('/patch', 'patch');
        $r->post('/post', 'post');
        $r->put('/put', 'put');
        $r->options('/options', 'options');

        $expected = [
            ['DELETE', '/delete', 'delete', []],
            ['GET', '/get', 'get', []],
            ['HEAD', '/head', 'head', []],
            ['PATCH', '/patch', 'patch', []],
            ['POST', '/post', 'post', []],
            ['PUT', '/put', 'put', []],
            ['OPTIONS', '/options', 'options', []],
        ];

        $this->assertSame($expected, $r->routes);
    }

    public function testMiddleware()
    {
        $r = new DummyRouteCollector();
        $r->delete('/delete', 'delete', ['middleware_delete']);
        $r->get('/get', 'get', ['middleware_get']);
        $r->head('/head', 'head', ['middleware_head']);
        $r->patch('/patch', 'patch', ['middleware_patch']);
        $r->post('/post', 'post', ['middleware_post']);
        $r->put('/put', 'put', ['middleware_put']);
        $r->options('/options', 'options', ['middleware_options']);

        $r->addGroup('/group-one', function (DummyRouteCollector $r) {
            $r->delete('/delete', 'delete', ['middleware_delete']);
            $r->get('/get', 'get', ['middleware_get']);
            $r->head('/head', 'head', ['middleware_head']);
            $r->patch('/patch', 'patch', ['middleware_patch']);
            $r->post('/post', 'post', ['middleware_post']);
            $r->put('/put', 'put', ['middleware_put']);
            $r->options('/options', 'options', ['middleware_options']);
        }, ['middleware_group']);

        $expected = [
            ['DELETE', '/delete', 'delete', ['middleware_delete']],
            ['GET', '/get', 'get', ['middleware_get']],
            ['HEAD', '/head', 'head', ['middleware_head']],
            ['PATCH', '/patch', 'patch', ['middleware_patch']],
            ['POST', '/post', 'post', ['middleware_post']],
            ['PUT', '/put', 'put', ['middleware_put']],
            ['OPTIONS', '/options', 'options', ['middleware_options']],

            ['DELETE', '/group-one/delete', 'delete', ['middleware_group', 'middleware_delete']],
            ['GET', '/group-one/get', 'get', ['middleware_group', 'middleware_get']],
            ['HEAD', '/group-one/head', 'head', ['middleware_group', 'middleware_head']],
            ['PATCH', '/group-one/patch', 'patch', ['middleware_group', 'middleware_patch']],
            ['POST', '/group-one/post', 'post', ['middleware_group', 'middleware_post']],
            ['PUT', '/group-one/put', 'put', ['middleware_group', 'middleware_put']],
            ['OPTIONS', '/group-one/options', 'options', ['middleware_group', 'middleware_options']]
        ];

        $this->assertSame($expected, $r->routes);

        $r = new DummyRouteCollector();
        $r->addGroup('/group', function (DummyRouteCollector $r) {
            $r->addGroup('/sub-group', function (DummyRouteCollector $r) {
                $r->addGroup('/sub-sub-group', function (DummyRouteCollector $r) {
                    $r->get('/get', 'get', ['middleware_get']);
                });
            });
        });

        $this->assertSame(
            [
                [
                    'GET',
                    '/group/sub-group/sub-sub-group/get',
                    'get',
                    ['middleware_get']
                ]
            ],
            $r->routes
        );


        $r = new DummyRouteCollector();
        $r->addGroup('/group', function (DummyRouteCollector $r) {
            $r->addGroup('/sub-group', function (DummyRouteCollector $r) {
                $r->addGroup('/sub-sub-group', function (DummyRouteCollector $r) {
                    $r->get('/get', 'get', ['middleware_get']);
                });
            });
        }, ['group_1','group_2']);

        $this->assertSame(
            [
                [
                    'GET',
                    '/group/sub-group/sub-sub-group/get',
                    'get',
                    ['group_1', 'group_2', 'middleware_get']
                ]
            ],
            $r->routes
        );

        $r = new DummyRouteCollector();
        $r->addGroup('/group', function (DummyRouteCollector $r) {
            $r->addGroup('/sub-group', function (DummyRouteCollector $r) {
                $r->addGroup('/sub-sub-group', function (DummyRouteCollector $r) {
                    $r->get('/get', 'get', ['middleware_get']);
                }, ['sub-sub-group_1']);
            }, ['sub-group_1']);
        }, ['group_1']);

        $this->assertSame(
            [
                [
                    'GET',
                    '/group/sub-group/sub-sub-group/get',
                    'get',
                    ['group_1', 'sub-group_1', 'sub-sub-group_1','middleware_get']
                ]
            ],
            $r->routes
        );
    }

    public function testGroups()
    {
        $r = new DummyRouteCollector();

        $r->delete('/delete', 'delete');
        $r->get('/get', 'get');
        $r->head('/head', 'head');
        $r->patch('/patch', 'patch');
        $r->post('/post', 'post');
        $r->put('/put', 'put');
        $r->options('/options', 'options');

        $r->addGroup('/group-one', function (DummyRouteCollector $r) {
            $r->delete('/delete', 'delete');
            $r->get('/get', 'get');
            $r->head('/head', 'head');
            $r->patch('/patch', 'patch');
            $r->post('/post', 'post');
            $r->put('/put', 'put');
            $r->options('/options', 'options');

            $r->addGroup('/group-two', function (DummyRouteCollector $r) {
                $r->delete('/delete', 'delete');
                $r->get('/get', 'get');
                $r->head('/head', 'head');
                $r->patch('/patch', 'patch');
                $r->post('/post', 'post');
                $r->put('/put', 'put');
                $r->options('/options', 'options');
            });
        });

        $r->addGroup('/admin', function (DummyRouteCollector $r) {
            $r->get('-some-info', 'admin-some-info');
        });
        $r->addGroup('/admin-', function (DummyRouteCollector $r) {
            $r->get('more-info', 'admin-more-info');
        });

        $expected = [
            ['DELETE', '/delete', 'delete', []],
            ['GET', '/get', 'get', []],
            ['HEAD', '/head', 'head', []],
            ['PATCH', '/patch', 'patch', []],
            ['POST', '/post', 'post', []],
            ['PUT', '/put', 'put', []],
            ['OPTIONS', '/options', 'options', []],
            ['DELETE', '/group-one/delete', 'delete', []],
            ['GET', '/group-one/get', 'get', []],
            ['HEAD', '/group-one/head', 'head', []],
            ['PATCH', '/group-one/patch', 'patch', []],
            ['POST', '/group-one/post', 'post', []],
            ['PUT', '/group-one/put', 'put', []],
            ['OPTIONS', '/group-one/options', 'options', []],
            ['DELETE', '/group-one/group-two/delete', 'delete', []],
            ['GET', '/group-one/group-two/get', 'get', []],
            ['HEAD', '/group-one/group-two/head', 'head', []],
            ['PATCH', '/group-one/group-two/patch', 'patch', []],
            ['POST', '/group-one/group-two/post', 'post', []],
            ['PUT', '/group-one/group-two/put', 'put', []],
            ['OPTIONS', '/group-one/group-two/options', 'options', []],
            ['GET', '/admin-some-info', 'admin-some-info', []],
            ['GET', '/admin-more-info', 'admin-more-info', []],
        ];

        $this->assertSame($expected, $r->routes);
    }
}

class DummyRouteCollector extends RouteCollector
{
    public $routes = [];

    public function __construct()
    {
    }

    public function addRoute($httpMethod, $route, $handler, $middleware = [], $name = null)
    {
        if (!empty($this->currentMiddleware)) {
            array_unshift($middleware, ...$this->currentMiddleware);
        }

        $route = $this->currentGroupPrefix . $route;
        $this->routes[] = [$httpMethod, $route, $handler, $middleware];
    }
}
