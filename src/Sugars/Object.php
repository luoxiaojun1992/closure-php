<?php

namespace Lxj\ClosurePHP\Sugars\Object;

/**
 * @param $class
 * @param array $constructParameters
 * @return array
 * @throws \Exception
 */
function newObject($class, $constructParameters = [])
{
    global $classDefinitions;

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    return [
        'class' => $class
    ];
}

/**
 * @param $objectData
 * @param $method
 * @param $scope
 * @param array $parameters
 * @return mixed
 * @throws \Exception
 */
function callObjectMethod($objectData, $method, $scope, $parameters = [])
{
    global $classDefinitions;

    $class = $objectData['class'];

    if (!isset($classDefinitions[$class])) {
        throw new \Exception('Class ' . $class . ' not existed');
    }

    if (!isset($classDefinitions[$class]['methods']['instance'][$scope][$method])) {
        if (isset($classDefinitions[$class]['extends'])) {
            $parentObjectData = $objectData;
            $parentObjectData['class'] = $classDefinitions[$class]['extends'];
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

    if (!$classDefinitions[$class]['loaded']) {
        require_once $classDefinitions[$class]['ƒile_path'];
        $classDefinitions[$class]['loaded'] = true;
    }

    $functionName = $classDefinitions[$class]['namespace'] . '\\' .
        'Class' . str_replace('\\', '_', $objectData['class'])
        . 'Instance' . ucfirst($scope) . 'Func' . ucfirst($method);
    return call_user_func_array($functionName, $parameters);
}
