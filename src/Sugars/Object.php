<?php

namespace Lxj\ClosurePHP\Sugars\Object;

use Lxj\ClosurePHP\Sugars\Scope;

/**
 * @param $objectData
 * @param $propName
 * @param $callback
 * @param string $scope
 * @throws \Exception
 */
function modifyObjectProp(&$objectData, $propName, $callback, $scope = Scope::PUBLIC)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $matchedProp = false;
    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['props']['instance'][$availableScope][$propName])) {
            $matchedProp = true;
            break;
        }
    }

    if (!$matchedProp) {
        if (isset($classDefinition['extends'])) {
            $currentClass = $objectData['class'];
            $objectData['class'] = $classDefinition['extends'];
            if ($scope === Scope::PRIVATE) {
                $parentScope = Scope::PROTECTED;
            } else {
                $parentScope = $scope;
            }
            modifyObjectProp($objectData, $propName, $callback, $parentScope);
            $objectData['class'] = $currentClass;
        } else {
            throw new \Exception('Prop ' . $propName . ' of Class ' . $class . ' not existed');
        }
    } else {
        $callback($objectData);
    }
}

/**
 * @param $objectData
 * @param $propName
 * @param $value
 * @param string $scope
 * @throws \Exception
 */
function setObjectProp(&$objectData, $propName, $value, $scope = Scope::PUBLIC)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $matchedProp = false;
    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['props']['instance'][$availableScope][$propName])) {
            $matchedProp = true;
            break;
        }
    }

    if (!$matchedProp) {
        if (isset($classDefinition['extends'])) {
            $currentClass = $objectData['class'];
            $objectData['class'] = $classDefinition['extends'];
            if ($scope === Scope::PRIVATE) {
                $parentScope = Scope::PROTECTED;
            } else {
                $parentScope = $scope;
            }
            setObjectProp($objectData, $propName, $value, $parentScope);
            $objectData['class'] = $currentClass;
        } else {
            throw new \Exception('Prop ' . $propName . ' of Class ' . $class . ' not existed');
        }
    } else {
        $objectData['props'][$propName] = $value;
    }
}

/**
 * @param $objectData
 * @param $propName
 * @param string $scope
 * @return null
 * @throws \Exception
 */
function accessObjectProp($objectData, $propName, $scope = Scope::PUBLIC)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $matchedProp = false;
    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['props']['instance'][$availableScope][$propName])) {
            $matchedProp = true;
            break;
        }
    }

    if (!$matchedProp) {
        if (isset($classDefinition['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinition['extends'];
            if ($scope === Scope::PRIVATE) {
                $parentScope = Scope::PROTECTED;
            } else {
                $parentScope = $scope;
            }
            return accessObjectProp($parentObjectData, $propName, $parentScope);
        } else {
            throw new \Exception('Prop ' . $propName . ' of Class ' . $class . ' not existed');
        }
    }

    return $objectData['props'][$propName] ?? null;
}

/**
 * @param $objectData
 * @return mixed
 * @throws \Exception
 */
function setObjectPropDefaultValue($objectData)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    if (isset($classDefinition['extends'])) {
        $parentObjectData = $objectData;
        $parentObjectData['class'] = $classDefinition['extends'];
        $objectData = setObjectPropDefaultValue($parentObjectData);
        $objectData['class'] = $class;
    }

    if (isset($classDefinition['props']['instance'])) {
        foreach ($classDefinition['props']['instance'] as $scopeProps) {
            foreach ($scopeProps as $scope => $propInfo) {
                if (array_key_exists('default', $propInfo)) {
                    $objectData['props'][$propInfo['name']] = $propInfo['default'];
                }
            }
        }
    }

    return $objectData;
}

/**
 * @param $class
 * @param string $scope
 * @param array $constructParameters
 * @return array
 * @throws \Exception
 */
function newObject($class, $scope = Scope::PUBLIC, $constructParameters = [])
{
    global $classDefinitions;

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $objectData = [
        'class' => $class,
    ];

    $objectData = setObjectPropDefaultValue($objectData);

    $constructMethod = '__construct';
    if (!isset($classDefinition['methods']['instance'][$scope][$constructMethod])) {
        return $objectData;
    }

    callObjectMethod($objectData, '__construct', $scope, $constructParameters, false);

    return $objectData;
}

/**
 * @param $objectData
 * @param $method
 * @param $scope
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function callStaticObjectMethod($objectData, $method, $scope = Scope::PUBLIC, $parameters = []) {
    return callObjectMethod($objectData, $method, $scope, $parameters, true);
}

/**
 * @param $objectData
 * @param $method
 * @param string $scope
 * @param array $parameters
 * @param false $isStatic
 * @param null $originObjectData
 * @return mixed
 * @throws \Exception
 */
function callObjectMethod($objectData, $method, $scope = Scope::PUBLIC, $parameters = [], $isStatic = false, $originObjectData = null)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $staticOrInstance = $isStatic ? 'static' : 'instance';

    $matchedMethod = false;
    $targetScope = null;

    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['methods'][$staticOrInstance][$availableScope][$method])) {
            $matchedMethod = true;
            $targetScope = $availableScope;
            break;
        }
    }

    if (!$matchedMethod) {
        if (isset($classDefinition['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinition['extends'];
            if ($scope === Scope::PRIVATE) {
                $parentScope = Scope::PROTECTED;
            } else {
                $parentScope = $scope;
            }
            return callObjectMethod(
                $parentObjectData, $method, $parentScope, $parameters, $isStatic,
                $originObjectData ?: $objectData
            );
        } else {
            throw new \Exception('Method ' . $method . ' of Class ' . $class . ' not existed');
        }
    }

    if (!$classDefinition['loaded']) {
        require_once $classDefinition['ƒile_path'];
        $classDefinitions[$class]['loaded'] = true;
    }

    $functionName = $classDefinition['namespace'] . '\\' .
        'Class' . str_replace('\\', '_', $objectData['class'])
        . ucfirst($staticOrInstance) . ucfirst($targetScope) . 'Func' . ucfirst($method);
    return call_user_func_array($functionName, array_merge($parameters, [$originObjectData ?: $objectData]));
}

/**
 * @param $scope
 * @return array
 * @throws \Exception
 */
function availableScopes($scope)
{
    $availableScopes = [];

    if ($scope === Scope::PUBLIC) {
        $availableScopes = [
            Scope::PUBLIC,
        ];
    } elseif ($scope === Scope::PROTECTED) {
        $availableScopes = [
            Scope::PROTECTED,
            Scope::PUBLIC,
        ];
    } elseif ($scope === Scope::PRIVATE) {
        $availableScopes = [
            Scope::PRIVATE,
            Scope::PROTECTED,
            Scope::PUBLIC,
        ];
    } else {
        throw new \Exception('Unknown scope ' . $scope);
    }

    return $availableScopes;
}
