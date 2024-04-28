<?php

namespace php\core\utils\lang;

class ArrayUtils
{

    public static function any(\Closure $lambda, array $array): bool
    {
        foreach ($array as $elt) {
            $resFn = call_user_func($lambda, $elt);
            if (!is_bool($resFn)) {
                throw new \Error("ArrayUtils::any() : le paramètre lambda doit renvoyer un booléen");
            }
            if ($resFn) {
                return true;
            }
        }

        return false;
    }

    public static function findOne(\Closure $lambda, array &$array)
    {
        foreach ($array as $elt) {
            $resFn = call_user_func($lambda, $elt);
            if (!is_bool($resFn)) {
                throw new \Error("ArrayUtils::findOne() : le paramètre lambda doit renvoyer un booléen");
            }
            if ($resFn) {
                return $elt;
            }
        }

        return null;
    }

    public static function find(\Closure $lambda, array &$array): array
    {
        $retArray = [];
        foreach ($array as $elt) {
            $resFn = call_user_func($lambda, $elt);
            if (!is_bool($resFn)) {
                throw new \Error("ArrayUtils::find() : le paramètre lambda doit renvoyer un booléen");
            }
            if ($resFn) {
                $retArray[] = $elt;
            }
        }

        return $retArray;
    }

    public static function where(\Closure $lambda, array &$array): array
    {
        $retArray = [];
        foreach ($array as $k => $elt) {
            $resFn = call_user_func($lambda, $elt);
            if (!is_bool($resFn)) {
                throw new \Error("ArrayUtils::where() : le paramètre lambda doit renvoyer un booléen");
            }
            if ($resFn) {
                $retArray[$k] = $elt;
            }
        }

        return $retArray;
    }

    public static function first(array &$array)
    {
        return $array[array_key_first($array)];
    }

    public static function last(array &$array)
    {
        return $array[array_key_last($array)];
    }

    public static function skip(array $array, int $nToSkip)
    {
        return array_slice($array, $nToSkip);
    }

    public static function skipThenTake(array $array, int $nToSkip, int $nToTake)
    {
        return array_slice($array, $nToSkip, $nToTake);
    }

    public static function take(array $array, int $nToTake)
    {
        return array_slice($array, 0, $nToTake);
    }

    public static function takeAndSkip(array $array, int $nToTake, int $nToSkip)
    {
        return self::skip(array_slice($array, 0, $nToTake), $nToSkip);
    }

    public static function toStdClass(array $navBarLink)
    {

        $retObj = new \stdClass();

        if (!self::isAssocArray($navBarLink)) {
            return $navBarLink;
        }

        foreach ($navBarLink as $k => $v) {
            $vv = $v;
            if (is_array($v)) {
                $vv = self::toStdClass($vv);
            }
            $retObj->$k = $vv;
        }

        return $retObj;
    }

    public static function isAssocArray(array $arr): bool
    {
        if (array() === $arr) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param string $key
     * @param array $array
     * @return mixed|null
     */
    public static function tryGet(string $key, array $array, $dftValue = null)
    {
        if (key_exists($key, $array)) {
            return $array[$key];
        }

        return $dftValue;
    }

    /**
     * @param array $array
     * @return string
     */
    public static function toString($array): string
    {
        $retStr = "";
        if (is_string($array)) {
            return $array;
        } else if (is_object($array)) {
            return strval($array);
        } else if (is_array($array)) {

            foreach ($array as $k => $v) {
                $retStr .= "\"$k\" => '";
                if (is_array($v)) {
                    $retStr .= '[' . self::toString($v) . ']';
                } else {
                    $retStr .= $v;
                }

                $retStr .= "', ";
            }
        }

        return $retStr;

    }

    /**
     * Vérifie qu'un ensemble de valeurs se trouvent dans un tableau (test AND).
     * @param array $array
     * @param $rep
     * @return bool
     */
    public static function inArrayMultipleVal(array $array, $rep) : bool
    {
        foreach ($array as $a) {
            if (!in_array($a, $rep)) {
                return false;
            }
        }
        return true;
    }

    public static function implodeAssoc($glue, $array, $template = "%s: %s" ) {
        $result = '';

        foreach ($array as $key => $value) {
            $result .= sprintf($template, $key, $value) . $glue;
        }

        // Supprimer le dernier séparateur ajouté
        $result = rtrim($result, $glue);

        return $result;
    }

    public static function addRange(array $subArray, array &$targetArray)
    {
        foreach ($subArray as $elt) {
            $targetArray[] = $elt;
        }

    }


}