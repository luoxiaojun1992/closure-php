<?php

namespace Lxj\ClosurePHP\Demo;

use Lxj\ClosurePHP\Compiler\Compiler;

class Bar extends Compiler
{
    const BAR_CONST = 'bar_const';

    public static $barPubStatAttr = 'bar_pub_stat_attr';

    public $barPubAttr;

    protected $barProAttr;

    private $barPriAttr;

    function helloBar()
    {
        return 'Bar';
    }
}