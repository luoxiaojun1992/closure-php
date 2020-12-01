<?php

namespace Lxj\ClosurePHP\Demo;

class Foo
{
    public $fooPubAttr = 'foo_pub_attr';

	protected $fooProAttr;

	private $fooPriAttr;

	public function helloPubFoo()
	{
		return 'Pub Foo';
	}

    protected function helloProFoo()
    {
        return 'Pro Foo';
    }

    private function helloPriFoo()
    {
        return 'Pri Foo';
    }
}
