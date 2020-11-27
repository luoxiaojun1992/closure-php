<?php

namespace Lxj\ClosurePHP\Sugars\Object;

/**
 * @param $objectData
 * @param $propName
 * @param $value
 * @param string $scope
 * @return mixed
 * @throws \Exception
 */
function setObjectProp($objectData, $propName, $value, $scope = 'public')
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    if (!isset($classDefinition['props']['instance'][$scope][$propName])) {
        if (isset($classDefinition['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinition['extends'];
            if ($scope === 'private') {
                $parentScope = 'protected';
            } else {
                $parentScope = $scope;
            }
            $newObjectData = setObjectProp($parentObjectData, $propName, $value, $parentScope);
            $newObjectData['class'] = $objectData['class'];
            return $newObjectData;
        } else {
            return $objectData;
        }
    }

    $objectData['props'][$propName] = $value;
    return $objectData;
}

/**
 * @param $objectData
 * @param $propName
 * @param string $scope
 * @return null
 * @throws \Exception
 */
function accessObjectProp($objectData, $propName, $scope = 'public')
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    if (!isset($classDefinition['props']['instance'][$scope][$propName])) {
        if (isset($classDefinition['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinition['extends'];
            if ($scope === 'private') {
                $parentScope = 'protected';
            } else {
                $parentScope = $scope;
            }
            return accessObjectProp($parentObjectData, $propName, $parentScope);
        } else {
            return null;
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

    if (isset($classDefinition['props']['instance'])) {
        foreach ($classDefinition['props']['instance'] as $scopeProps) {
            foreach ($scopeProps as $propInfo) {
                if (array_key_exists('default', $propInfo)) {
                    $objectData['props'][$propInfo['name']] = $propInfo['default'];
                }
            }
        }
    }

    if (isset($classDefinition['extends'])) {
        $parentObjectData = $objectData;
        $parentObjectData['class'] = $classDefinition['extends'];
        $newObjectData = setObjectPropDefaultValue($parentObjectData);
        $newObjectData['class'] = $objectData['class'];
        return $newObjectData;
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
function newObject($class, $scope = 'public', $constructParameters = [])
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
function callStaticObjectMethod($objectData, $method, $scope = 'public', $parameters = []) {
    return callObjectMethod($objectData, $method, $scope, $parameters, true);
}

/**
 * @param $objectData
 * @param $method
 * @param $scope
 * @param array $parameters
 * @param false $isStatic
 * @return mixed
 * @throws \Exception
 */
function callObjectMethod($objectData, $method, $scope = 'public', $parameters = [], $isStatic = false)
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    $classDefinition = $classDefinitions[$class];

    $staticOrInstance = $isStatic ? 'static' : 'instance';

    if (!isset($classDefinition['methods'][$staticOrInstance][$scope][$method])) {
        if (isset($classDefinition['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinition['extends'];
            if ($scope === 'private') {
                $parentScope = 'protected';
            } else {
                $parentScope = $scope;
            }
            return callObjectMethod($parentObjectData, $method, $parentScope, $parameters);
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
        . ucfirst($staticOrInstance) . ucfirst($scope) . 'Func' . ucfirst($method);
    return call_user_func_array($functionName, array_merge($parameters, [$objectData]));
}
