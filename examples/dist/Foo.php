<?php

namespace Lxj\ClosurePHP\Demo;

function ClassLxj_ClosurePHP_Demo_FooInstancePublicFuncHelloPubFoo(&$thisObj)
{
    return 'Pub Foo';
}
function ClassLxj_ClosurePHP_Demo_FooInstanceProtectedFuncHelloProFoo(&$thisObj)
{
    return 'Pro Foo';
}
function ClassLxj_ClosurePHP_Demo_FooInstancePrivateFuncHelloPriFoo(&$thisObj)
{
    return 'Pri Foo';
}