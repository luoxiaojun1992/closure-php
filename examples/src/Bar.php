<?php

namespace Lxj\ClosurePHP\Demo;

use Lxj\ClosurePHP\Compiler\Compiler;

class Bar extends Compiler
{
    public $barPubAttr;

    protected $barProAttr;

    private $barPriAttr;

    function helloBar()
    {
        return 'Bar';
    }
}
