<?php
namespace App\Ecofy\Support;

class ObjectAccessor
{
    public static function get($obj, $propertyPath, $default = null)
    {
        if (is_array($obj)) {
            return ObjectAccessor::getFromArr($obj, $propertyPath, $default);
        } else {
            return ObjectAccessor::getFromObject($obj, $propertyPath, $default);
        }
    }

    public static function getFromObject($obj, $propertyPath, $default = null)
    {
        $propPathArr = explode('.', $propertyPath);
        $currNode = $obj;
        foreach($propPathArr as $prop) {
            if (property_exists($currNode, $prop)) {
                $currNode = $currNode->$prop;
            } else {
                $currNode = $default;
                break;
            }
        }
        return $currNode;
    }

    // @todo : merge two, so combination of object and arry can be traversed
    public static function getFromArr($obj, $propertyPath, $default = null)
    {
        $propPathArr = explode('.', $propertyPath);
        $currNode = $obj;
        foreach($propPathArr as $prop) {
            if (array_key_exists($prop, $currNode)) {
                $currNode = $currNode[$prop];
            } else {
                $currNode = $default;
                break;
            }
        }
        return $currNode;
    }
}
