<?php

class FunctionTest extends \PHPUnit\Framework\TestCase
{
    protected function compile($filePath)
    {
        return (new \Lxj\ClosurePHP\Compiler\Compiler())->compilePath($filePath);
    }

    protected function generateCode($compileResult, $outputDir)
    {
        list($classDefinitions, $compiledCode) = $compileResult;

        foreach ($classDefinitions as $className => $classDefinition) {
            $compiledClassCode = $compiledCode[$className];
            $filePath = $outputDir . '/' . $classDefinition['file'];
            $classDefinitions[$className]['ƒile_path'] = $filePath;
            file_put_contents($filePath, $compiledClassCode);
        }

        $metaFilePath = $outputDir . '/meta.php';
        file_put_contents(
            $metaFilePath,
            '<?php' . str_repeat(PHP_EOL, 2) .
            'global $classDefinitions;' . str_repeat(PHP_EOL, 2) .
            '$classDefinitions = ' .
            var_export($classDefinitions, true) . ';' . PHP_EOL
        );

        include_once $metaFilePath;
    }

    /**
     * @throws Exception
     */
    public function testCompileAndCallMethod()
    {
        $compiledCodeDir = __DIR__ . '/../output/tests/compile_and_call_method';
        if (!is_dir($compiledCodeDir)) {
            mkdir($compiledCodeDir, 0777, true);
        }
        $this->generateCode(
            $this->compile(__DIR__ . '/../examples/src'),
            realpath($compiledCodeDir)
        );

        $barObj = \Lxj\ClosurePHP\Sugars\Object\newObject('Lxj\\ClosurePHP\\Demo\\Bar');

        $this->assertEquals(
            'Foo',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloFoo',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );
        $this->assertEquals(
            'Bar',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloBar',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );

        $this->assertEquals(
            'Foo',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloFoo',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'Bar',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloBar',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );

        $this->assertEquals(
            'Foo',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloFoo',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'Bar',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloBar',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        $this->assertEquals(
            'bar_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );
        $this->assertEquals(
            'foo_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );

        $this->assertEquals(
            'bar_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'foo_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );

        $this->assertEquals(
            'bar_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'foo_pub_attr',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
            $barObj,
            'barPubAttr',
            'bar_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
        );
        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

            $barObj,
            'fooPubAttr',
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
        );

        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
            $barObj,
            'barPubAttr',
            'bar_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
        );
        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

            $barObj,
            'fooPubAttr',
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
        );

        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
            $barObj,
            'barPubAttr',
            'bar_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
        );
        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

            $barObj,
            'fooPubAttr',
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
        );

        $this->assertEquals(
            'bar_pub_attr_new',
            Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );

        $this->assertEquals(
            'bar_pub_attr_new',
            Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );

        $this->assertEquals(
            'bar_pub_attr_new',
            Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPubAttr', function (&$barObj) {
            $barObj['props']['barPubAttr'] = 'bar_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PUBLIC);
        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
            $barObj['props']['fooPubAttr'] = 'foo_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PUBLIC);

        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPubAttr', function (&$barObj) {
            $barObj['props']['barPubAttr'] = 'bar_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PROTECTED);
        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
            $barObj['props']['fooPubAttr'] = 'foo_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PROTECTED);

        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPubAttr', function (&$barObj) {
            $barObj['props']['barPubAttr'] = 'bar_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PRIVATE);
        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
            $barObj['props']['fooPubAttr'] = 'foo_pub_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PRIVATE);

        $this->assertEquals(
            'bar_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PUBLIC
            )
        );

        $this->assertEquals(
            'bar_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );

        $this->assertEquals(
            'bar_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'foo_pub_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooPubAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
    }
}
