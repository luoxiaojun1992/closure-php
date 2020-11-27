<?php

namespace Lxj\ClosurePHP\Compiler;

use PhpParser\PrettyPrinter\Standard;

class Compiler
{
    protected $parser;

    protected $transformer;

    public function __construct($parser = null, $transformer = null)
    {
        $this->parser = $parser ?: (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
        $this->transformer = $transformer ?: (new Transformer());
    }

    public function compile($classCode)
    {
        $ast = $this->parseAst($classCode);
        list($classDefinitions, $transformedAst) = $this->transform($ast);
        $transformedCode = [];
        foreach ($transformedAst as $className => $transformedClassAst) {
            $transformedCode[$className] = $this->generateCode([$transformedClassAst]);
        }
        return [$classDefinitions, $transformedCode];
    }

    protected function removePHPTag($code)
    {
        return substr(ltrim($code), 5);
    }

    public function compilePath($fileOrDir)
    {
        $classCodeArr = [];

        if (is_dir($fileOrDir)) {
            $fd = opendir($fileOrDir);
            while ($subFileOrDir = readdir($fd)) {
                if (!in_array($subFileOrDir, ['.', '..'])) {
                    $filePath = $fileOrDir . '/' . $subFileOrDir;
                    $classCodeArr[] = $this->removePHPTag(file_get_contents($filePath));
                }
            }
            closedir($fd);
        } else {
            $classCodeArr[] = $this->removePHPTag(file_get_contents($fileOrDir));
        }

        return $this->compile(
            '<?php' . str_repeat(PHP_EOL, 2) .
            implode(
                str_repeat(PHP_EOL, 2),
                $classCodeArr
            )
        );
    }

    protected function parseAst($code)
    {
        return $this->parser->parse($code);
    }

    protected function transform($ast)
    {
        return $this->transformer->transform($ast);
    }

    protected function generateCode($ast)
    {
        return (new Standard())->prettyPrintFile($ast);
    }
}
