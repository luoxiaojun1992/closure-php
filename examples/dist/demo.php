<?php

require_once __DIR__ . '/../../vendor/autoload.php';

global $classDefinitions;
$classDefinitions = [
    'Foo' => [
        'loaded' => false,
        'file' => __DIR__ . '/Foo.php',
        'methods' => [
            'public' => [
                'hello' => []
            ]
        ]
    ]
];

$fooObj = \Lxj\ClosurePHP\Sugars\Object\newObject('Foo');
echo \Lxj\ClosurePHP\Sugars\Object\callObjectMethod($fooObj, 'hello', 'public'), PHP_EOL;
