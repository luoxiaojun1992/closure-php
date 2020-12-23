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
                                            'default' => 'foo_pub_attr',
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
                                    'helloPubFoo' =>
                                        array (
                                            'name' => 'helloPubFoo',
                                            'scope' => 'public',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_FooInstancePublicFuncHelloPubFoo',
                                        ),
                                ),
                            'protected' =>
                                array (
                                    'helloProFoo' =>
                                        array (
                                            'name' => 'helloProFoo',
                                            'scope' => 'protected',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_FooInstanceProtectedFuncHelloProFoo',
                                        ),
                                ),
                            'private' =>
                                array (
                                    'helloPriFoo' =>
                                        array (
                                            'name' => 'helloPriFoo',
                                            'scope' => 'private',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_FooInstancePrivateFuncHelloPriFoo',
                                        ),
                                ),
                        ),
                ),
            'ƒile_path' => __DIR__ . '/Foo.php',
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
                                            'compiled_var_name' => 'ClassLxj_ClosurePHP_Demo_BarStaticPublicPropBarPubStatAttr',
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
                                            'default' => 'bar_pub_attr',
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
                                    'compiled_var_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_BarConstBAR_CONST',
                                ),
                        ),
                ),
            'methods' =>
                array (
                    'instance' =>
                        array (
                            'public' =>
                                array (
                                    'helloPubBar' =>
                                        array (
                                            'name' => 'helloPubBar',
                                            'scope' => 'public',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_BarInstancePublicFuncHelloPubBar',
                                        ),
                                ),
                            'protected' =>
                                array (
                                    'helloProBar' =>
                                        array (
                                            'name' => 'helloProBar',
                                            'scope' => 'protected',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_BarInstanceProtectedFuncHelloProBar',
                                        ),
                                ),
                            'private' =>
                                array (
                                    'helloPriBar' =>
                                        array (
                                            'name' => 'helloPriBar',
                                            'scope' => 'private',
                                            'is_static' => false,
                                            'compiled_func_name' => 'Lxj\\ClosurePHP\\Demo\\ClassLxj_ClosurePHP_Demo_BarInstancePrivateFuncHelloPriBar',
                                        ),
                                ),
                        ),
                ),
            'extends' => 'Lxj\\ClosurePHP\\Demo\\Foo',
            'ƒile_path' => __DIR__ . '/Bar.php',
        ),
);

$barObj = \Lxj\ClosurePHP\Sugars\Object\newObj('Lxj\\ClosurePHP\\Demo\\Bar');
echo \Lxj\ClosurePHP\Sugars\Object\call($barObj, 'helloPubFoo', 'public'), PHP_EOL;
echo \Lxj\ClosurePHP\Sugars\Object\call($barObj, 'helloPubBar', 'public'), PHP_EOL;
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'fooPubAttr', 'public'));
\Lxj\ClosurePHP\Sugars\Object\set($barObj, 'barPubAttr', 'bar_pub_attr_new', 'public');
\Lxj\ClosurePHP\Sugars\Object\set($barObj, 'fooPubAttr', 'foo_pub_attr_new', 'public');
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'fooPubAttr', 'public'));
\Lxj\ClosurePHP\Sugars\Object\access($barObj, 'barPubAttr', function (&$barObj) {
    $barObj['p_barPubAttr'] = 'bar_pub_attr_new_new';
}, 'public');
\Lxj\ClosurePHP\Sugars\Object\access($barObj, 'fooPubAttr', function (&$barObj) {
    $barObj['p_fooPubAttr'] = 'foo_pub_attr_new_new';
}, 'public');
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\get($barObj, 'fooPubAttr', 'public'));
