<?php

namespace Lxj\ClosurePHP\Demo;

global $ClassLxj_ClosurePHP_Demo_BarStaticPublicPropBarPubStatAttr;
$ClassLxj_ClosurePHP_Demo_BarStaticPublicPropBarPubStatAttr = 'bar_pub_stat_attr';
global $ClassLxj_ClosurePHP_Demo_BarInstancePublicPropBarPubAttr;
$ClassLxj_ClosurePHP_Demo_BarInstancePublicPropBarPubAttr = 'bar_pub_attr';
global $ClassLxj_ClosurePHP_Demo_BarInstanceProtectedPropBarProAttr;
global $ClassLxj_ClosurePHP_Demo_BarInstancePrivatePropBarPriAttr;
define('ClassLxj_ClosurePHP_Demo_BarConstBAR_CONST', 'bar_const');
function ClassLxj_ClosurePHP_Demo_BarInstancePublicFuncHelloBar()
{
    return 'Bar';
}