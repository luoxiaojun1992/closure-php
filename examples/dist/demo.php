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

$barObj = \Lxj\ClosurePHP\Sugars\Object\newObject('Lxj\\ClosurePHP\\Demo\\Bar');
echo \Lxj\ClosurePHP\Sugars\Object\callObjectMethod($barObj, 'helloFoo', 'public'), PHP_EOL;
echo \Lxj\ClosurePHP\Sugars\Object\callObjectMethod($barObj, 'helloBar', 'public'), PHP_EOL;
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'fooPubAttr', 'public'));
\Lxj\ClosurePHP\Sugars\Object\setObjectProp($barObj, 'barPubAttr', 'bar_pub_attr_new', 'public');
\Lxj\ClosurePHP\Sugars\Object\setObjectProp($barObj, 'fooPubAttr', 'foo_pub_attr_new', 'public');
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'fooPubAttr', 'public'));
\Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPubAttr', function (&$barObj) {
    $barObj['props']['barPubAttr'] = 'bar_pub_attr_new_new';
}, 'public');
\Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
    $barObj['props']['fooPubAttr'] = 'foo_pub_attr_new_new';
}, 'public');
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'barPubAttr', 'public'));
var_dump(\Lxj\ClosurePHP\Sugars\Object\accessObjectProp($barObj, 'fooPubAttr', 'public'));
