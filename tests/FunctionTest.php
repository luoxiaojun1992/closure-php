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
    public function testPublicMethodsAndProps()
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

        $testScopes = [
            \Lxj\ClosurePHP\Sugars\Scope::PUBLIC,
            \Lxj\ClosurePHP\Sugars\Scope::PROTECTED,
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE,
        ];

        foreach ($testScopes as $scope) {
            $this->assertEquals(
                'Pub Foo',
                \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                    $barObj,
                    'helloPubFoo',
                    $scope
                )
            );
            $this->assertEquals(
                'Pub Bar',
                \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                    $barObj,
                    'helloPubBar',
                    $scope
                )
            );
        }

        foreach ($testScopes as $scope) {
            $this->assertEquals(
                'bar_pub_attr',
                \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'barPubAttr',
                    $scope
                )
            );
            $this->assertEquals(
                'foo_pub_attr',
                \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'fooPubAttr',
                    $scope
                )
            );
        }

        foreach ($testScopes as $scope) {
            \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
                $barObj,
                'barPubAttr',
                'bar_pub_attr_new',
                $scope
            );
            \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

                $barObj,
                'fooPubAttr',
                'foo_pub_attr_new',
                $scope
            );
        }

        foreach ($testScopes as $scope) {
            $this->assertEquals(
                'bar_pub_attr_new',
                Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'barPubAttr',
                    $scope
                )
            );
            $this->assertEquals(
                'foo_pub_attr_new',
                \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'fooPubAttr',
                    $scope
                )
            );
        }

        foreach ($testScopes as $scope) {
            \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPubAttr', function (&$barObj) {
                $barObj['props']['barPubAttr'] = 'bar_pub_attr_new_new';
            }, $scope);
            \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
                $barObj['props']['fooPubAttr'] = 'foo_pub_attr_new_new';
            }, $scope);
        }

        foreach ($testScopes as $scope) {
            $this->assertEquals(
                'bar_pub_attr_new_new',
                \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'barPubAttr',
                    $scope
                )
            );
            $this->assertEquals(
                'foo_pub_attr_new_new',
                \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                    $barObj,
                    'fooPubAttr',
                    $scope
                )
            );
        }
    }

    /**
     * @throws Exception
     */
    public function testProtectedMethodsAndProps()
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
            'Pro Bar',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloProBar',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'Pro Foo',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloProFoo',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'Pro Foo',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloProFoo',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        $this->assertNull(
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertNull(
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertNull(
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
            $barObj,
            'barProAttr',
            'bar_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
        );
        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

            $barObj,
            'fooProAttr',
            'foo_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
        );
        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(

            $barObj,
            'fooProAttr',
            'foo_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
        );

        $this->assertEquals(
            'bar_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'foo_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'foo_pro_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barProAttr', function (&$barObj) {
            $barObj['props']['barProAttr'] = 'bar_pro_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PRIVATE);
        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
            $barObj['props']['fooProAttr'] = 'foo_pro_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PROTECTED);
        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'fooPubAttr', function (&$barObj) {
            $barObj['props']['fooProAttr'] = 'foo_pro_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PRIVATE);

        $this->assertEquals(
            'bar_pro_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
        $this->assertEquals(
            'foo_pro_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PROTECTED
            )
        );
        $this->assertEquals(
            'foo_pro_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'fooProAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
    }

    public function testPrivateMethodsAndProps()
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
            'Pri Bar',
            \Lxj\ClosurePHP\Sugars\Object\callObjectMethod(
                $barObj,
                'helloPriBar',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        $this->assertNull(
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPriAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\setObjectProp(
            $barObj,
            'barPriAttr',
            'bar_pri_attr_new',
            \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
        );

        $this->assertEquals(
            'bar_pri_attr_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPriAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );

        \Lxj\ClosurePHP\Sugars\Object\modifyObjectProp($barObj, 'barPriAttr', function (&$barObj) {
            $barObj['props']['barPriAttr'] = 'bar_pri_attr_new_new';
        }, \Lxj\ClosurePHP\Sugars\Scope::PRIVATE);

        $this->assertEquals(
            'bar_pri_attr_new_new',
            \Lxj\ClosurePHP\Sugars\Object\accessObjectProp(
                $barObj,
                'barPriAttr',
                \Lxj\ClosurePHP\Sugars\Scope::PRIVATE
            )
        );
    }
}
