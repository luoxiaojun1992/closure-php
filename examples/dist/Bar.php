<?php

namespace Lxj\ClosurePHP\Demo;

global $ClassLxj_ClosurePHP_Demo_BarStaticPublicPropBarPubStatAttr;
$ClassLxj_ClosurePHP_Demo_BarStaticPublicPropBarPubStatAttr = 'bar_pub_stat_attr';
define('ClassLxj_ClosurePHP_Demo_BarConstBAR_CONST', 'bar_const');
function ClassLxj_ClosurePHP_Demo_BarInstancePublicFuncHelloPubBar(&$thisObj)
{
    return 'Pub Bar';
}
function ClassLxj_ClosurePHP_Demo_BarInstanceProtectedFuncHelloProBar(&$thisObj)
{
    return 'Pro Bar';
}
function ClassLxj_ClosurePHP_Demo_BarInstancePrivateFuncHelloPriBar(&$thisObj)
{
    return 'Pri Bar';
}