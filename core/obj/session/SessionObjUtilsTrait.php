<?php

namespace myphpfw\core\obj\session;

use myphpfw\core\utils\Utils;

trait SessionObjUtilsTrait
{

    private string $prefix = "";
    function &__get($name)
    {
        return Utils::getStringFromArrayOrDefault( $this->prefix.$name, $_SESSION);
    }

    function __set($name, $value)
    {
        $_SESSION[$this->prefix.$name] = $value;
    }

}