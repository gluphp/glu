<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Glu\DependencyInjection\Service;
use Glu\In;
use Glu\App;
use Glu\Http\Request;

class MyServiceA {
    public function __construct(public MyServiceB $b)
    {

    }

    public function bye()
    {
        return '__'.$this->b->hello();
    }
}
class MyServiceB {
    public function hello()
    {
        return 'B';
    }
}

$app = new App(
    services: [
        new Service('bbb', '\\MyServiceB'),
        new Service('aaa', '\\MyServiceA', ['bbb']),
        new Service('foo_controller', FooController::class)
    ],
    listeners: [
        'on_bar' => [
            'bbb:hello'
        ]
    ]
);

class FooController {
    public function __invoke(In $in): string
    {
        throw new RuntimeException('Error: vbla bvloa vbla');
        return 'oh yeah';
    }
}

$app->path('home', 'GET', '/dummy/{one}', function (In $in) {
    echo $in->service('aaa')->bye();die();
    return 'foooo ' . $in->parameter('one');
}, null);

$app->path('two', 'GET', '/two', 'foo_controller');

$app->run(new Request('GET', '/two',[], [], [], [], [], '1.1.1.1'));
