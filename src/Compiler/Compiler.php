<?php

namespace Lxj\ClosurePHP\Compiler;

class Compiler
{
    protected $parser;

    protected $transformer;

    public function __construct($parser = null, $transformer = null)
    {
        $this->parser = $parser ?: (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
        $this->transformer = $transformer ?: (new Transformer());
    }

    public function compile($code)
    {
        $ast = $this->parseAst($code);
        $transformedAst = $this->transform($ast);
        return $this->generateCode($transformedAst);
    }

    public function compilePath($filePath)
    {
        return $this->compile(file_get_contents($filePath));
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

    }
}
