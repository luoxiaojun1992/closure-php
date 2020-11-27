<?php

namespace Lxj\ClosurePHP\Compiler;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;

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
                        if ($classBodyStmt instanceof Property) {
                            $propertyStmt = $classBodyStmt;

                            if ($propertyStmt->isPublic()) {
                                $propertyScope = 'public';
                            } elseif ($propertyStmt->isProtected()) {
                                $propertyScope = 'protected';
                            } elseif ($propertyStmt->isPrivate()) {
                                $propertyScope = 'private';
                            } elseif ($propertyStmt->isStatic()) {
                                $propertyScope = 'static';
                            } else {
                                $propertyScope = 'unknown';
                            }

                            foreach ($propertyStmt->props as $propertyPropStmt) {
                                $propertyInfo  = [];
                                $propertyInfo['scope'] = $propertyScope;
                                $propertyInfo['name'] = $propertyPropStmt->name->name;
                                if ($propertyPropStmt->default) {
                                    $propertyInfo['default'] = $propertyPropStmt->default->value;
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
    public function transform($ast)
    {
        $allClassInfo = $this->parseClassInfo($ast);

        $classDefinitions = [];

        $transformedAst = [];

        foreach ($allClassInfo as $className => $classInfo) {
            $classDefinition = [
                'file' => $classInfo['realName'] . '.php',
            ];

            $namespaceStmt = new Namespace_();
            $namespaceStmt->name = (new Name($classInfo['namespace']));

            $fileStmts = [];
            if (isset($classInfo['useStmt'])) {
                $fileStmts[] = $classInfo['useStmt'];
            }

            foreach ($classInfo['methods'] as $methodName => $methodInfo) {
                $staticOrInstance = $methodInfo['is_static'] ? 'static' : 'instance';
                $liteMethodInfo = $methodInfo;
                unset($liteMethodInfo['stmts']);
                $classDefinition['methods'][$staticOrInstance][$methodInfo['scope']][$methodInfo['name']] = $liteMethodInfo;

                $functionStmt = new Function_(
                    'Class' . ucfirst(str_replace('\\', '_', $className)) .
                    ($methodInfo['is_static'] ? 'Static' : 'Instance') .
                    ucfirst($methodInfo['scope']) . 'Func' . ucfirst($methodName)
                );
                $functionStmt->stmts = $methodInfo['stmts'];
                $fileStmts[] = $functionStmt;
            }

            $classDefinitions[$classInfo['name']] = $classDefinition;

            $namespaceStmt->stmts = $fileStmts;

            $transformedAst[] = $namespaceStmt;
        }

        var_dump($classDefinitions);

        return $transformedAst;
    }
}
