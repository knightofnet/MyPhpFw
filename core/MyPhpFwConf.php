<?php

namespace myphpfw\core;

use myphpfw\core\annotation\PropertyLinked;
use myphpfw\core\utils\lang\ReflectionUtils;

class MyPhpFwConf
{

    public static bool $IS_DEBUG = false;

    public static int $APP_LOG_LVL = LOGLVL_ERROR;

    public static ?string $URL_ROOT = null;

    public static ?string $SITE_NAME = null;

    public static ?string $INNER_SITE_NAME = null;

    public static ?string $USER_CLASS = null;
    public static ?string $USER_ID_FIELD_NAME = null;
    public static ?string $USER_USERNAME_FIELD_NAME = null;
    public static ?string $USER_USERAPITOKEN_FIELD_NAME = null;

    public static ?string $DBB_NAME = null;

    public static ?string $DBB_USER = null;

    public static ?string $DBB_PWD = null;

    public static ?string $DBB_HOST = null;

    public static string $SITE_PREFIX_URL = "?action=";

    public static string $BLADE_COMPILED_PATH = SVR_ROOT . "/view/c";

    public static ?string $SITE_KEY = null;


    public static ?string $LOG_FILENAME = null;


    private static $propertiesLoadedByAnnotation = [];

    /**
     * @throws \Exception
     */
    public static function initConf(bool $isLoadFromConstant = true)
    {
        if (!defined('SVR_ROOT')) {
            throw new \Exception("La constante SVR_ROOT n'est pas défini");
        }



        if ($isLoadFromConstant) {
            self::loadConfFromConstant();
            self::loadProperties();
            self::loadConfFromConstant(1);

        } else {
            self::loadProperties();
        }

        self::verifyConf();


    }

    private static function loadProperties()
    {
        if (is_null(self::$USER_ID_FIELD_NAME)) {

            /** @var null|PropertyLinked $propUniqueIdName */
            $propUniqueIdName = ReflectionUtils::getAnnotationOnMethod(self::$USER_CLASS, "getId", PropertyLinked::class);
            if (!is_null($propUniqueIdName)) {
                //throw new \Exception("USER_ID_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);


                self::$USER_ID_FIELD_NAME = $propUniqueIdName->getPropertyName();
                self::$propertiesLoadedByAnnotation[] = "USER_ID_FIELD_NAME";
            }

        }


        if (is_null(self::$USER_USERNAME_FIELD_NAME)) {

            /** @var null|PropertyLinked $propUniqueIdName */
            $propUniqueIdName = ReflectionUtils::getAnnotationOnMethod(self::$USER_CLASS, "getUsername", PropertyLinked::class);
            if (!is_null($propUniqueIdName)) {
                //throw new \Exception("USERNAME_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);


                self::$USER_USERNAME_FIELD_NAME = $propUniqueIdName->getPropertyName();
                self::$propertiesLoadedByAnnotation[] = "USER_USERNAME_FIELD_NAME";
            }

        }
        if (is_null(self::$USER_USERAPITOKEN_FIELD_NAME)) {

            /** @var null|PropertyLinked $propUniqueIdName */
            $propUniqueIdName = ReflectionUtils::getAnnotationOnMethod(self::$USER_CLASS, "getUserApiToken", PropertyLinked::class);
            if (!is_null($propUniqueIdName)) {
                //throw new \Exception("USER_USERAPITOKEN_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);


                self::$USER_USERAPITOKEN_FIELD_NAME = $propUniqueIdName->getPropertyName();
                self::$propertiesLoadedByAnnotation[] = "USER_USERAPITOKEN_FIELD_NAME";
            }

        }
    }

    private static function loadConfFromConstant(int $part = 0)
    {
        if ($part == 0) {
            self::$IS_DEBUG = self::getContantOrNull("IS_DEBUG");
            self::$APP_LOG_LVL = self::getContantOrNull("APP_LOG_LVL");
            self::$URL_ROOT = self::getContantOrNull("URL_ROOT");
            self::$SITE_NAME = self::getContantOrNull("SITE_NAME");
            self::$INNER_SITE_NAME = self::getContantOrNull("INNER_SITE_NAME");
            self::$USER_CLASS = self::getContantOrNull("USER_CLASS");

            self::$DBB_NAME = self::getContantOrNull("DBB_NAME");
            self::$DBB_USER = self::getContantOrNull("DBB_USER");
            self::$DBB_PWD = self::getContantOrNull("DBB_PWD");
            self::$DBB_HOST = self::getContantOrNull("DBB_HOST");
            self::$SITE_PREFIX_URL = self::getContantOrNull("SITE_PREFIX_URL");
            self::$BLADE_COMPILED_PATH = self::getContantOrNull("BLADE_COMPILED_PATH");
            self::$SITE_KEY = self::getContantOrNull("SITE_KEY");
            self::$LOG_FILENAME = self::getContantOrNull("LOG_FILENAME");
        } else if ($part == 1) {
            if (!in_array("USER_ID_FIELD_NAME", self::$propertiesLoadedByAnnotation)) {
                self::$USER_ID_FIELD_NAME = USER_ID_FIELD_NAME;
            }
            if (!in_array("USER_USERNAME_FIELD_NAME", self::$propertiesLoadedByAnnotation)) {
                self::$USER_USERNAME_FIELD_NAME = USERNAME_FIELD_NAME;
            }
            if (!in_array("USER_USERAPITOKEN_FIELD_NAME", self::$propertiesLoadedByAnnotation)) {
                self::$USER_USERAPITOKEN_FIELD_NAME = USER_USERAPITOKEN_FIELD_NAME;
            }
        }


    }

    /**
     *
     * @return void
     * @throws \Exception
     */
    private static function verifyConf()
    {

        if (is_null(self::$URL_ROOT)) {
            throw new \Exception("URL_ROOT n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$SITE_NAME)) {
            throw new \Exception("SITE_NAME n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$INNER_SITE_NAME)) {
            throw new \Exception("INNER_SITE_NAME n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$USER_CLASS)) {
            throw new \Exception("USER_CLASS n'est pas défini dans MyPhpFwConf");
        }
        // teste que la classe self::$USER_CLASS existe
        if (!class_exists(self::$USER_CLASS)) {
            throw new \Exception("La classe " . self::$USER_CLASS . " n'existe pas");
        }


        if (is_null(self::$USER_ID_FIELD_NAME)) {
            throw new \Exception("USER_ID_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);
        }

        if (is_null(self::$USER_USERNAME_FIELD_NAME)) {
            throw new \Exception("USERNAME_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);
        }
        if (is_null(self::$USER_USERAPITOKEN_FIELD_NAME)) {
            throw new \Exception("USER_USERAPITOKEN_FIELD_NAME n'est pas défini dans MyPhpFwConf ou getUsername n'est pas annoté par PropertyLinked dans la classe " . self::$USER_CLASS);
        }

        if (is_null(self::$DBB_NAME)) {
            throw new \Exception("DBB_NAME n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$DBB_USER)) {
            throw new \Exception("DBB_USER n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$DBB_PWD)) {
            throw new \Exception("DBB_PWD n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$DBB_HOST)) {
            throw new \Exception("DBB_HOST n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$SITE_KEY)) {
            throw new \Exception("SITE_KEY n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$LOG_FILENAME)) {
            throw new \Exception("LOG_FILENAME n'est pas défini dans MyPhpFwConf");
        }


    }

    private static function getContantOrNull(string $cstName) : ?string
    {
        if (!defined($cstName)) {
            return null;
        }
        return constant($cstName);
    }

}