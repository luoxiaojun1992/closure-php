<?php

namespace Lxj\ClosurePHP\Sugars\Object;

use Lxj\ClosurePHP\Sugars\Scope;

/**
 * @param $objectData
 * @param $propName
 * @param $callback
 * @param string $scope
 * @return mixed
 * @throws \Exception
 */
function accessObjectProp(&$objectData, $propName, $callback, $scope = Scope::PUBLIC)
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
            $modifyResult = accessObjectProp($objectData, $propName, $callback, $parentScope);
            $objectData['class'] = $currentClass;
            return $modifyResult;
        } else {
            throw new \Exception('Prop ' . $propName . ' of Class ' . $class . ' not existed');
        }
    } else {
        return $callback($objectData);
    }
}

/**
 * @param $objectData
 * @param $propName
 * @param $callback
 * @param string $scope
 * @return mixed
 * @throws \Exception
 */
function access(&$objectData, $propName, $callback, $scope = Scope::PUBLIC)
{
    return accessObjectProp($objectData, $propName, $callback, $scope);
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
 * @param $value
 * @param string $scope
 * @throws \Exception
 */
function set(&$objectData, $propName, $value, $scope = Scope::PUBLIC)
{
    setObjectProp($objectData, $propName, $value, $scope);
}

/**
 * @param $objectData
 * @param $propName
 * @param string $scope
 * @return null
 * @throws \Exception
 */
function getObjectProp($objectData, $propName, $scope = Scope::PUBLIC)
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
            return getObjectProp($parentObjectData, $propName, $parentScope);
        } else {
            throw new \Exception('Prop ' . $propName . ' of Class ' . $class . ' not existed');
        }
    }

    return $objectData['props'][$propName] ?? null;
}

/**
 * @param $objectData
 * @param $propName
 * @param string $scope
 * @return null
 * @throws \Exception
 */
function get($objectData, $propName, $scope = Scope::PUBLIC)
{
    return getObjectProp($objectData, $propName, $scope);
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

    callObjectMethod($objectData, '__construct', $scope, $constructParameters);

    return $objectData;
}

/**
 * @param $class
 * @param string $scope
 * @param array $constructParameters
 * @return array
 * @throws \Exception
 */
function newObj($class, $scope = Scope::PUBLIC, $constructParameters = [])
{
    return newObject($class, $scope, $constructParameters);
}

/**
 * @param $objectData
 * @return mixed
 * @throws \Exception
 */
function getClass($objectData)
{
    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    return $class;
}

/**
 * @param $objectData
 * @param $method
 * @param string $scope
 * @param array $parameters
 * @param null $originObjectData
 * @return mixed
 * @throws \Exception
 */
function callObjectMethod(&$objectData, $method, $scope = Scope::PUBLIC, $parameters = [], &$originObjectData = null)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $staticOrInstance = 'instance';

    $matchedMethod = false;
    $functionName = null;

    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['methods'][$staticOrInstance][$availableScope][$method])) {
            $matchedMethod = true;
            $functionName = $classDefinition['methods'][$staticOrInstance][$availableScope][$method]['compiled_func_name'];
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
            if (!$originObjectData) {
                $originObjectData = &$objectData;
            }
            return callObjectMethod(
                $parentObjectData, $method, $parentScope, $parameters,
                $originObjectData
            );
        } else {
            throw new \Exception('Method ' . $method . ' of Class ' . $class . ' not existed');
        }
    }

    if (!$classDefinition['loaded']) {
        include_once $classDefinition['ƒile_path'];
        $classDefinitions[$class]['loaded'] = true;
    }

    if ($originObjectData) {
        $parameters[] = &$originObjectData;
    } else {
        $parameters[] = &$objectData;
    }

    return call_user_func_array($functionName, $parameters);
}

/**
 * @param $objectData
 * @param $method
 * @param string $scope
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function call(&$objectData, $method, $scope = Scope::PUBLIC, $parameters = [])
{
    return callObjectMethod(
        $objectData,
        $method,
        $scope,
        $parameters
    );
}

/**
 * @param $class
 * @param $method
 * @param $scope
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function callStaticObjectMethod($class, $method, $scope = Scope::PUBLIC, $parameters = []) {
    global $classDefinitions;

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $staticOrInstance = 'static';

    $matchedMethod = false;
    $functionName = null;

    foreach (availableScopes($scope) as $availableScope) {
        if (isset($classDefinition['methods'][$staticOrInstance][$availableScope][$method])) {
            $matchedMethod = true;
            $functionName = $classDefinition['methods'][$staticOrInstance][$availableScope][$method]['compiled_func_name'];
            break;
        }
    }

    if (!$matchedMethod) {
        if (isset($classDefinition['extends'])) {
            if ($scope === Scope::PRIVATE) {
                $parentScope = Scope::PROTECTED;
            } else {
                $parentScope = $scope;
            }
            return callStaticObjectMethod(
                $classDefinition['extends'], $method, $parentScope, $parameters
            );
        } else {
            throw new \Exception('Method ' . $method . ' of Class ' . $class . ' not existed');
        }
    }

    if (!$classDefinition['loaded']) {
        include_once $classDefinition['ƒile_path'];
        $classDefinitions[$class]['loaded'] = true;
    }

    return call_user_func_array($functionName, $parameters);
}

/**
 * @param $class
 * @param $method
 * @param string $scope
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function callStatic($class, $method, $scope = Scope::PUBLIC, $parameters = [])
{
    return callStaticObjectMethod($class, $method, $scope, $parameters);
}

function getThisObject($args)
{
    return $args[count($args) - 1];
}

function thisObj($args)
{
    return getThisObject($args);
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
