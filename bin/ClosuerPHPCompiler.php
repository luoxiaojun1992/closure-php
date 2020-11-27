#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Invalid arguments.';
    echo PHP_EOL;
    exit(1);
}

$path = $argv[1];

echo (new Lxj\ClosurePHP\Compiler\Compiler())->compilePath($path), PHP_EOL;
