<?php

namespace myphpfw\core;

class MyPhpFwConf
{

    public static bool $IS_DEBUG = false;

    public static int $APP_LOG_LVL = LOGLVL_ERROR;

    public static ?string $URL_ROOT = null;

    public static ?string $SITE_NAME = null;

    public static ?string $INNER_SITE_NAME = null;

    public static ?string $USER_CLASS = null;

    public static ?string $DBB_NAME = null;

    public static ?string $DBB_USER = null;

    public static ?string $DBB_PWD = null;

    public static ?string $DBB_HOST = null;

    public static string $SITE_PREFIX_URL = "?action=";

    public static string $BLADE_COMPILED_PATH = SVR_ROOT . "/view/c";

    public static ?string $SITE_KEY = null;


    public static ?string $LOG_FILENAME = null;
    public static $ROUTE_LOGIN = null;
    public static $ROUTE_HOME = null;


    /**
     * @throws \Exception
     */
    public static function initConf(bool $isLoadFromConstant = true)
    {
        if(!defined('SRV_ROOT')){
            throw new \Exception("La constante SRV_ROOT n'est pas défini");
        }

        if ($isLoadFromConstant) {
            self::loadConfFromConstant();
        }

        self::verifyConf();



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

        if (is_null(self::$ROUTE_LOGIN)) {
            throw new \Exception("ROUTE_LOGIN n'est pas défini dans MyPhpFwConf");
        }
        if (is_null(self::$ROUTE_HOME)) {
            throw new \Exception("ROUTE_HOME n'est pas défini dans MyPhpFwConf");
        }

    }

    private static function loadConfFromConstant()
    {
        self::$IS_DEBUG = IS_DEBUG;
        self::$APP_LOG_LVL = APP_LOG_LVL;
        self::$URL_ROOT = URL_ROOT;
        self::$SITE_NAME = SITE_NAME;
        self::$INNER_SITE_NAME = INNER_SITE_NAME;
        self::$USER_CLASS = USER_CLASS;
        self::$DBB_NAME = DBB_NAME;
        self::$DBB_USER = DBB_USER;
        self::$DBB_PWD = DBB_PWD;
        self::$DBB_HOST = DBB_HOST;
        self::$SITE_PREFIX_URL = SITE_PREFIX_URL;
        self::$BLADE_COMPILED_PATH = BLADE_COMPILED_PATH;
        self::$SITE_KEY = SITE_KEY;
        self::$LOG_FILENAME = LOG_FILENAME;
    }

}