<?php

namespace Lxj\ClosurePHP\Demo;

class Bar extends Foo
{
    const BAR_CONST = 'bar_const';

    public static $barPubStatAttr = 'bar_pub_stat_attr';

    public $barPubAttr = 'bar_pub_attr';

    protected $barProAttr;

    private $barPriAttr;

    public function helloPubBar()
    {
        return 'Pub Bar';
    }

    protected function helloProBar()
    {
        return 'Pro Bar';
    }

    private function helloPriBar()
    {
        return 'Pri Bar';
    }
}
