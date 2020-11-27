#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($argv[1])) {
    echo 'Invalid arguments.';
    echo PHP_EOL;
    exit(1);
}

if (!isset($argv[2])) {
    $argv[2] = realpath(__DIR__ . '/../output');
}

$path = $argv[1];

$outputDir = $argv[2];

list($classDefinitions, $compiledCode) = (new Lxj\ClosurePHP\Compiler\Compiler())->compilePath($path);

foreach ($classDefinitions as $className => $classDefinition) {
    $compiledClassCode = $compiledCode[$className];
    $filePath = $outputDir . '/' . $classDefinition['file'];
    $classDefinitions[$className]['ƒile_path'] = $filePath;
    file_put_contents($filePath, $compiledClassCode);
}

var_export($classDefinitions);

echo PHP_EOL;
