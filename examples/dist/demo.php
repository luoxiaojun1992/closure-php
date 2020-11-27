<?php

require_once __DIR__ . '/../../vendor/autoload.php';

global $classDefinitions;
$classDefinitions = array (
    'Lxj\\ClosurePHP\\Demo\\Foo' =>
        array (
            'loaded' => false,
            'file' => 'Foo.php',
            'namespace' => 'Lxj\\ClosurePHP\\Demo',
            'props' =>
                array (
                    'instance' =>
                        array (
                            'public' =>
                                array (
                                    'fooPubAttr' =>
                                        array (
                                            'scope' => 'public',
                                            'is_static' => false,
                                            'name' => 'fooPubAttr',
                                        ),
                                ),
                            'protected' =>
                                array (
                                    'fooProAttr' =>
                                        array (
                                            'scope' => 'protected',
                                            'is_static' => false,
                                            'name' => 'fooProAttr',
                                        ),
                                ),
                            'private' =>
                                array (
                                    'fooPriAttr' =>
                                        array (
                                            'scope' => 'private',
                                            'is_static' => false,
                                            'name' => 'fooPriAttr',
                                        ),
                                ),
                        ),
                ),
            'methods' =>
                array (
                    'instance' =>
                        array (
                            'public' =>
                                array (
                                    'helloFoo' =>
                                        array (
                                            'name' => 'helloFoo',
                                            'scope' => 'public',
                                            'is_static' => false,
                                        ),
                                ),
                        ),
                ),
            'ƒile_path' => '/Users/luoxiaojun/php/closure-php/examples/dist/Foo.php',
        ),
    'Lxj\\ClosurePHP\\Demo\\Bar' =>
        array (
            'loaded' => false,
            'file' => 'Bar.php',
            'namespace' => 'Lxj\\ClosurePHP\\Demo',
            'props' =>
                array (
                    'static' =>
                        array (
                            'public' =>
                                array (
                                    'barPubStatAttr' =>
                                        array (
                                            'scope' => 'public',
                                            'is_static' => true,
                                            'name' => 'barPubStatAttr',
                                            'default' => 'bar_pub_stat_attr',
                                        ),
                                ),
                        ),
                    'instance' =>
                        array (
                            'public' =>
                                array (
                                    'barPubAttr' =>
                                        array (
                                            'scope' => 'public',
                                            'is_static' => false,
                                            'name' => 'barPubAttr',
                                        ),
                                ),
                            'protected' =>
                                array (
                                    'barProAttr' =>
                                        array (
                                            'scope' => 'protected',
                                            'is_static' => false,
                                            'name' => 'barProAttr',
                                        ),
                                ),
                            'private' =>
                                array (
                                    'barPriAttr' =>
                                        array (
                                            'scope' => 'private',
                                            'is_static' => false,
                                            'name' => 'barPriAttr',
                                        ),
                                ),
                        ),
                ),
            'consts' =>
                array (
                    'BAR_CONST' =>
                        array (
                            0 =>
                                array (
                                    'name' => 'BAR_CONST',
                                    'value' => 'bar_const',
                                ),
                        ),
                ),
            'methods' =>
                array (
                    'instance' =>
                        array (
                            'public' =>
                                array (
                                    'helloBar' =>
                                        array (
                                            'name' => 'helloBar',
                                            'scope' => 'public',
                                            'is_static' => false,
                                        ),
                                ),
                        ),
                ),
            'extends' => 'Lxj\\ClosurePHP\\Demo\\Foo',
            'ƒile_path' => '/Users/luoxiaojun/php/closure-php/examples/dist/Bar.php',
        ),
);

$fooObj = \Lxj\ClosurePHP\Sugars\Object\newObject('Lxj\\ClosurePHP\\Demo\\Bar');
echo \Lxj\ClosurePHP\Sugars\Object\callObjectMethod($fooObj, 'helloFoo', 'public'), PHP_EOL;
echo \Lxj\ClosurePHP\Sugars\Object\callObjectMethod($fooObj, 'helloBar', 'public'), PHP_EOL;
