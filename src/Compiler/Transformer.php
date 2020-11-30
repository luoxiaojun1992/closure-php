<?php

namespace Lxj\ClosurePHP\Compiler;

use Lxj\ClosurePHP\Sugars\Scope;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Global_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\PrettyPrinter\Standard;

class Transformer
{
    protected function parseClassInfo($ast)
    {
        $allClassInfo = [];

        foreach ($ast as $classAst) {
            $classInfo = [];

            $namespace = implode('\\', $classAst->name->parts);
            $classInfo['namespace'] = $namespace;

            $classStmts = $classAst->stmts;

            foreach ($classStmts as $classStmt) {
                if ($classStmt instanceof Use_) {
                    $useStmt = $classStmt;

                    $classInfo['useStmt'] = $useStmt;

                    $uses = $useStmt->uses;
                    foreach ($uses as $useUse) {
                        $useInfo = [];
                        $useClassName = implode('\\', $useUse->name->parts);
                        $realUseClassName =  $useUse->name->parts[count($useUse->name->parts) - 1];
                        $useInfo['name'] = $useClassName;
                        $classInfo['uses'][$realUseClassName] = $useInfo;
                    }
                } elseif ($classStmt instanceof Class_) {
                    $classDefineStmt = $classStmt;

                    $className = $classDefineStmt->name->name;
                    $classInfo['realName'] = $className;
                    $classInfo['name'] = $namespace . '\\' . $className;

                    if ($classDefineStmt->extends) {
                        $extendsClassNameParts = $classDefineStmt->extends->parts;
                        $classInfo['extends'] = $extendsClassNameParts;
                    }

                    $classBodyStmts = $classDefineStmt->stmts;

                    foreach ($classBodyStmts as $classBodyStmt) {
                        if ($classBodyStmt instanceof ClassConst) {
                            $constStmt = $classBodyStmt;

                            foreach ($constStmt->consts as $constConstStmt) {
                                $constInfo  = [];
                                $constInfo['name'] = $constConstStmt->name->name;
                                $constInfo['value'] = $constConstStmt->value;
                                $classInfo['consts'][$constInfo['name']] = $constInfo;
                            }
                        } elseif ($classBodyStmt instanceof Property) {
                            $propertyStmt = $classBodyStmt;

                            if ($propertyStmt->isPublic()) {
                                $propertyScope = Scope::PUBLIC;
                            } elseif ($propertyStmt->isProtected()) {
                                $propertyScope = 'protected';
                            } elseif ($propertyStmt->isPrivate()) {
                                $propertyScope = 'private';
                            } else {
                                $propertyScope = 'unknown';
                            }

                            $propertyIsStatic = $propertyStmt->isStatic();

                            foreach ($propertyStmt->props as $propertyPropStmt) {
                                $propertyInfo  = [];
                                $propertyInfo['scope'] = $propertyScope;
                                $propertyInfo['is_static'] = $propertyIsStatic;
                                $propertyInfo['name'] = $propertyPropStmt->name->name;
                                if ($propertyPropStmt->default) {
                                    $propertyInfo['default'] = $propertyPropStmt->default;
                                }
                                $classInfo['props'][$propertyInfo['name']] = $propertyInfo;
                            }
                        } elseif ($classBodyStmt instanceof ClassMethod) {
                            $classMethodStmt = $classBodyStmt;

                            $classMethodInfo = [];

                            $classMethodInfo['name'] = $classMethodStmt->name->name;

                            if ($classMethodStmt->isPublic()) {
                                $classMethodInfo['scope'] = 'public';
                            } elseif ($classMethodStmt->isProtected()) {
                                $classMethodInfo['scope'] = 'protected';
                            } elseif ($classMethodStmt->isPrivate()) {
                                $classMethodInfo['scope'] = 'private';
                            } else {
                                $classMethodInfo['scope'] = 'unknown';
                            }

                            $classMethodInfo['is_static'] = $classMethodStmt->isStatic();

                            $classMethodBodyStmt = $classMethodStmt->stmts;
                            $classMethodInfo['stmts'] = $classMethodBodyStmt;

                            $classInfo['methods'][$classMethodInfo['name']] = $classMethodInfo;
                        }
                    }

                    $allClassInfo[$classInfo['name']] = $classInfo;
                }
            }
        }

        return $allClassInfo;
    }

    /**
     * @param Namespace_[] $ast
     * @return mixed
     */
    public function transform(array $ast)
    {
        $allClassInfo = $this->parseClassInfo($ast);

        $classDefinitions = [];

        $transformedAst = [];

        foreach ($allClassInfo as $className => $classInfo) {
            $classDefinition = [
                'loaded' => false,
                'file' => $classInfo['realName'] . '.php',
                'namespace' => $classInfo['namespace'],
            ];

            $namespaceStmt = new Namespace_();
            $namespaceStmt->name = (new Name($classInfo['namespace']));

            $fileStmts = [];

            if (isset($classInfo['useStmt'])) {
                $fileStmts[] = $classInfo['useStmt'];
            }

            if (isset($classInfo['props'])) {
                foreach ($classInfo['props'] as $propertyName => $propertyInfo) {
                    $propertyStaticOrInstance = $propertyInfo['is_static'] ? 'static' : 'instance';
                    $litePropertyInfo = $propertyInfo;
                    if (array_key_exists('default', $propertyInfo)) {
                        $propertyDefaultVal = (new Standard())->prettyPrintExpr($propertyInfo['default']);
                        if (is_string($propertyDefaultVal)) {
                            $litePropertyInfo['default'] = trim($propertyDefaultVal, '\'');
                        } else {
                            $litePropertyInfo['default'] = $propertyDefaultVal;
                        }
                    }
                    $classDefinition['props'][$propertyStaticOrInstance][$propertyInfo['scope']][$propertyName] = $litePropertyInfo;

                    if ($propertyInfo['is_static']) {
                        $varName = 'Class' . ucfirst(str_replace('\\', '_', $className)) .
                            ucfirst($propertyStaticOrInstance) .
                            ucfirst($propertyInfo['scope']) . 'Prop' . ucfirst($propertyName);
                        $varDeclareStmt = new Global_([
                            new Variable($varName)
                        ]);
                        $fileStmts[] = $varDeclareStmt;
                        if (array_key_exists('default', $propertyInfo)) {
                            $varAssignStmt = new Expression(
                                new Assign(
                                    new Variable($varName),
                                    $propertyInfo['default']
                                )
                            );
                            $fileStmts[] = $varAssignStmt;
                        }
                    }
                }
            }

            if (isset($classInfo['consts'])) {
                foreach ($classInfo['consts'] as $constName => $constInfo) {
                    $liteConstInfo = $constInfo;
                    unset($liteConstInfo['value']);
                    $classDefinition['consts'][$constName][] = $liteConstInfo;

                    $constVarName = 'Class' . ucfirst(str_replace('\\', '_', $className)) .
                        'Const' . ucfirst($constName);
                    $constVarDefineStmt = new Expression(
                        new FuncCall(
                            new Name('define'),
                            [new String_($constVarName), $constInfo['value']]
                        )
                    );
                    $fileStmts[] = $constVarDefineStmt;
                }
            }

            if (isset($classInfo['methods'])) {
                foreach ($classInfo['methods'] as $methodName => $methodInfo) {
                    $methodStaticOrInstance = $methodInfo['is_static'] ? 'static' : 'instance';
                    $liteMethodInfo = $methodInfo;
                    unset($liteMethodInfo['stmts']);
                    $classDefinition['methods'][$methodStaticOrInstance][$methodInfo['scope']][$methodInfo['name']] = $liteMethodInfo;

                    $functionStmt = new Function_(
                        'Class' . ucfirst(str_replace('\\', '_', $className)) .
                        ucfirst($methodStaticOrInstance) .
                        ucfirst($methodInfo['scope']) . 'Func' . ucfirst($methodName)
                    );
                    $functionStmt->stmts = $methodInfo['stmts'];
                    $fileStmts[] = $functionStmt;
                }
            }

            if (isset($classInfo['extends'])) {
                $extendClass = $classInfo['extends'];
                if (count($extendClass) > 1) {
                    $extendClassName = implode('\\', $extendClass);
                } else {
                    if (isset($classInfo['uses'])) {
                        if (isset($classInfo['uses'][$extendClass[0]])) {
                            $extendClassName = $classInfo['uses'][$extendClass[0]]['name'];
                        } else {
                            $extendClassName = $classInfo['namespace'] . '\\' . $extendClass[0];
                        }
                    } else {
                        $extendClassName = $classInfo['namespace'] . '\\' . $extendClass[0];
                    }
                }

                $classDefinition['extends'] = $extendClassName;
            }

            $classDefinitions[$className] = $classDefinition;

            $namespaceStmt->stmts = $fileStmts;

            $transformedAst[$className] = $namespaceStmt;
        }

        return [$classDefinitions, $transformedAst];
    }
}
