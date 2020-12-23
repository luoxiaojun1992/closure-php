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
        if ($ast) {
            list($classDefinitions, $transformedAst) = $this->transform($ast);
            $transformedCode = [];
            foreach ($transformedAst as $className => $transformedClassAst) {
                $transformedCode[$className] = $this->generateCode([$transformedClassAst]);
            }
            return [$classDefinitions, $transformedCode];
        }

        return [[], []];
    }

    protected function removePHPTag($code)
    {
        return substr(ltrim($code), 5);
    }

    public function compilePath($fileOrDir)
    {
        $compiledResult = [[], []];

        $classCodeArr = [];

        if (is_dir($fileOrDir)) {
            $fd = opendir($fileOrDir);
            while ($subFileOrDir = readdir($fd)) {
                if (!in_array($subFileOrDir, ['.', '..'])) {
                    $filePath = $fileOrDir . '/' . $subFileOrDir;
                    $subCompiledResult = $this->compilePath($filePath);
                    $compiledResult[0] = array_merge($compiledResult[0], $subCompiledResult[0]);
                    $compiledResult[1] = array_merge($compiledResult[1], $subCompiledResult[1]);
                }
            }
            closedir($fd);
        } else {
            $classCodeArr[] = $this->removePHPTag(file_get_contents($fileOrDir));
        }

        if ($classCodeArr) {
            $subCompiledResult = $this->compile(
                '<?php' . str_repeat(PHP_EOL, 2) .
                implode(
                    str_repeat(PHP_EOL, 2),
                    $classCodeArr
                )
            );
            $compiledResult[0] = array_merge($compiledResult[0], $subCompiledResult[0]);
            $compiledResult[1] = array_merge($compiledResult[1], $subCompiledResult[1]);
        }

        return $compiledResult;
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
