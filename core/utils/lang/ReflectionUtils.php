<?php

namespace myphpfw\core\utils\lang;

use Doctrine\Common\Annotations\AnnotationReader;
use myphpfw\core\utils\Utils;

class ReflectionUtils
{

    public static function isPropertyExists(string $propertyName, object $obj): bool
    {
        $reflectionClass = new \ReflectionClass($obj);

        foreach ($reflectionClass->getProperties() as $property) {
            $propName = $property->getName();
            if ($propertyName == $propName) {
                return true;
            }

        }

        return false;
    }

    public static function getPropertyValue(string $propertyName, object $obj)
    {
        $reflectionClass = new \ReflectionClass($obj);

        foreach ($reflectionClass->getProperties() as $property) {
            $propName = $property->getName();
            if ($propertyName != $propName) {
                continue;
            }

            $isPublicProp = $property->getModifiers() == \ReflectionProperty::IS_PUBLIC;
            if ($isPublicProp) {
                return $property->getValue($obj);
            }

            $getterName = "get" . StringUtils::ucFirst($propertyName);
            if ($reflectionClass->hasMethod($getterName)) {
                $getterMeth = $reflectionClass->getMethod($getterName);
                if ($getterMeth->getNumberOfParameters() == 0) {
                    return $getterMeth->invoke($obj);
                }
            }

        }

        return $obj;
    }

    public static function getProperties($obj, $propertiesNameExcluded = [], $class = null)
    {
        if ($obj instanceof \stdClass) {
            return get_object_vars($obj);
        }

        $reflection = new \ReflectionClass($class ?? $obj);
        $properties = $reflection->getProperties();
        $result = [];

        /** @var \ReflectionProperty $property */
        foreach ($properties as $property) {
            if ($property->isPrivate()) {
                $property->setAccessible(true);
            }

            if (in_array($property->getName(), $propertiesNameExcluded)) {
                continue;
            }

            $propertyName = $property->getName();
            $propertyValue = $property->getValue($obj);
            $result[$propertyName] = $propertyValue;
        }

        return $result;
    }

    public static function tryHydrateFrom(string $className, \stdClass $stdClass, $propertiesNameExcluded = [])
    {
        try {

            $objTargetClass = new $className();
            $targetClassProps = ReflectionUtils::getProperties($objTargetClass);

            $inputClassProps = ReflectionUtils::getProperties($stdClass);


            foreach ($inputClassProps as $prop => $val) {
                if (in_array($prop, $propertiesNameExcluded)) {
                    continue;
                }
                if (key_exists($prop, $targetClassProps)) {
                    $propProp = ReflectionUtils::getProperty($objTargetClass, $prop);
                    if ($val != null
                        && $propProp->hasType()
                        && $propProp->getType()->getName() === \DateTime::class) {
                        $val = new \DateTime($val->date, new \DateTimeZone($val->timezone));
                    }
                    ReflectionUtils::setValue($val, $prop, $objTargetClass, true);
                }
            }

            return $objTargetClass;

        } catch (\Exception $ex) {
            Utils::logWarn($ex);
            return null;
        }
    }

    public static function getProperty($objOrClass, string $propName): ?\ReflectionProperty
    {
        try {
            $reflectionClass = new \ReflectionClass($objOrClass);
            return $reflectionClass->getProperty($propName);
        } catch (\ReflectionException $e) {
            Utils::logError($e);
            return null;
        }

    }

    public static function setValue($value, string $propertyName, object $obj, bool $isForce = false)
    {
        $reflectionClass = new \ReflectionClass($obj);

        foreach ($reflectionClass->getProperties() as $property) {
            $propName = $property->getName();
            if ($propertyName != $propName) {
                continue;
            }

            if ($property->isPrivate() && $isForce) {
                $property->setAccessible(true);
            }
            if ($property->isPublic() || $isForce) {
                $property->setValue($obj, $value);
                return $obj;
            }


            $getterName = "set" . StringUtils::ucFirst($propertyName);
            if ($reflectionClass->hasMethod($getterName)) {
                $getterMeth = $reflectionClass->getMethod($getterName);
                if ($getterMeth->getNumberOfParameters() == 1) {
                    return $getterMeth->invoke($obj, $value);
                }
            }

        }

        return $obj;
    }

    public static function getConstantesOfClass(string $class): array
    {
        // Utilisez la réflexion (ReflectionClass) pour obtenir les constantes de la classe
        $reflection = new ReflectionClass($class);
        $constantes = $reflection->getConstants();

        return $constantes;
    }

    /**
     * Obtient une annotation sur une méthode
     *
     * @param string $fullClassName
     * @param string $methodName
     * @param string $annotationClassName
     * @return object|null
     * @throws \ReflectionException
     */
    public static function getAnnotationOnMethod(string $fullClassName, string $methodName, string $annotationClassName): ?object
    {

        $annotationReader = new AnnotationReader();
        $rf = new \ReflectionMethod($fullClassName, $methodName);
        return $annotationReader->getMethodAnnotation($rf, $annotationClassName);
    }


}