<?php

namespace Lxj\ClosurePHP\Demo;

class Foo
{
    public $fooPubAttr = 'foo_pub_attr';

	protected $fooProAttr;

	private $fooPriAttr;

	function helloFoo()
	{
		return 'Foo';
	}
}
