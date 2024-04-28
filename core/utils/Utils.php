<?php

namespace myphpfw\core\utils;

use myphpfw\core\App;
use myphpfw\core\MyPhpFwConf;
use myphpfw\core\utils\lang\StringUtils;

class Utils
{

    public static bool $isLogEcho = false;
    private static array $sizes = ["o", "ko", "Mo", "Go", "To"];

    /**
     * require_once_dir
     * @param string $serveur_dir le dossier serveur dont il faut inclure les fichiers php fils.
     * @return void
     */
    public static function require_once_dir(string $serveur_dir, array $ignoredFilesName)
    {
        $files = scandir($serveur_dir);
        $serveur_dir = rtrim($serveur_dir, '/\\');
        //var_dump($serveur_dir);
        foreach ($files as $file) {
            if ("." == $file || ".." == $file) continue;
            $fullPath = $serveur_dir . '/' . $file;
            if (is_dir($fullPath)) {
                self::require_once_dir($fullPath);
            } else if (substr($fullPath, -4) == ".php") {
                $doRequire = true;
                foreach ($ignoredFilesName as $ignoredFileName) {
                    if (StringUtils::str_ends_with($fullPath, $ignoredFileName)) {
                        $doRequire = false;
                        break;
                    }
                }

                if ($doRequire) {
                    require_once($fullPath);
                }
            }
        }
    }

    public static function result404(string $step = "")
    {
        http_response_code(404);
    }

    public static function result200()
    {
        http_response_code(200);
    }

    public static function log(...$whats)
    {
        try {
            foreach ($whats as $what) {
                $autoLvl = LOGLVL_INFO;
                if ($what instanceof \Throwable) {
                    $autoLvl = LOGLVL_ERROR;
                }
                $str = "";
                if (is_string($what)) {
                    $str = $what;
                } else {
                    $str = json_encode($what);
                }

                $logPrefix = "DEBUG:";
                switch ($autoLvl) {
                    case LOGLVL_INFO :
                        self::logInfo($str);
                        break;
                    case LOGLVL_WARN :
                        self::logWarn($str);
                        break;
                    case LOGLVL_ERROR :
                        self::logError($str);
                        break;
                    case LOGLVL_DEBUG :
                        self::logDebug($str);
                }


            }
        } catch (\Throwable $ex) {
            var_dump($ex);
            throw $ex;
        }
    }

    public static function logInfo(...$msgs)
    {
        if (MyPhpFwConf::$APP_LOG_LVL >= LOGLVL_INFO)
            self::logGen("INFO :", $msgs);
    }

    /**
     * Méthode logGen
     *
     * Cette méthode privée est utilisée pour générer des logs avec un préfixe spécifique et un ensemble de messages.
     * Elle parcourt chaque message, détermine son type et le convertit en une chaîne de caractères pour l'enregistrement du log.
     * Les messages sont ensuite enregistrés dans un fichier de log avec une date et un préfixe.
     *
     * @param string $prefix Le préfixe à ajouter au début de chaque message de log.
     * @param array $msgs Un tableau de messages à enregistrer dans le log.
     * @return void
     */
    private static function logGen($prefix, $msgs)
    {
        foreach ($msgs as $msg) {
            $str = "";
            if (is_null($msg)) {
                $str = "NULL";
            } elseif (is_string($msg)) {
                $str = $msg;
            } elseif (is_int($msg) || is_float($msg)) {
                $str = strval($msg);
            } else {
                $str = json_encode($msg);
            }

            error_log(date("d-m-Y-H:i-s") . ':' . $prefix . $str . "\n", 3, dirname($_SERVER['DOCUMENT_ROOT']) . '/' . MyPhpFwConf::$LOG_FILENAME);
        }
    }

    public static function logWarn(...$msgs)
    {
        if (MyPhpFwConf::$APP_LOG_LVL >= LOGLVL_WARN)
            self::logGen("WARN :", $msgs);
    }

    public static function logError(...$msgs)
    {
        if (MyPhpFwConf::$APP_LOG_LVL >= LOGLVL_ERROR)
            self::logGen("ERROR:", $msgs);
    }

    public static function logDebug(...$msgs)
    {
        if (MyPhpFwConf::$APP_LOG_LVL >= LOGLVL_DEBUG)
            self::logGen("DEBUG:", $msgs);
    }

    public static function logOld(...$whats)
    {
        try {
            if (count($whats) > 1) {

                file_put_contents(
                    dirname($_SERVER['DOCUMENT_ROOT']) . '/' . MyPhpFwConf::$LOG_FILENAME,
                    "#---- START ---- #" . "\n", FILE_APPEND);
            }
            foreach ($whats as $what) {
                $str = "";
                if (is_string($what)) {
                    $str = $what;
                } else {
                    ob_start();
                    var_dump($what);
                    $str = ob_get_clean();
                }

                $str = sprintf("%s : %s", date("d-m-Y-H-i-s"), $str);

                if (self::$isLogEcho) {
                    echo $str . "\n";
                }

                file_put_contents(
                    dirname($_SERVER['DOCUMENT_ROOT']) . '/' . MyPhpFwConf::$LOG_FILENAME,
                    $str . "\n", FILE_APPEND
                );
            }

            file_put_contents(
                dirname($_SERVER['DOCUMENT_ROOT']) . '/' . MyPhpFwConf::$LOG_FILENAME,
                "#---- _END_ ---- #" . "\n", FILE_APPEND);

        } catch (\Throwable $ex) {
            var_dump($ex);
            throw $ex;
        } finally {

        }
    }

    /**
     * Méthode rotateLogFile
     * Cette méthode est utilisée pour faire tourner les fichiers de log.
     *
     * @param $filePath
     * @return bool|string
     */
    public static function rotateLogFile($filePath)
    {
        $maxSize = 1024 * 1024; // 1 Mo

        if (file_exists($filePath) && filesize($filePath) >= $maxSize) {
            $backupPath = $filePath . '.' . date("Ymd-His");

            if (rename($filePath, $backupPath)) {
                file_put_contents($filePath, '');

                return $backupPath;
            }
        }

        return false;
    }

    public static function humanReadableSize(int $size, string $format = "{0:0.##} {1}"): string
    {
        $len = $size;
        $order = 0;
        while ($len >= 1024 && $order < count(self::$sizes) - 1) {
            $order++;
            $len = $len / 1024;
        }


        return sprintf("%.2f %s", $len, self::$sizes[$order]);

    }

    public static function &getStringFromArrayOrDefault(string $arrayKey, array &$array, ?object $dftVal = null)
    {
        if (key_exists($arrayKey, $array)) {
            return $array[$arrayKey];
        }
        return $dftVal;
    }

    public static function getBoolFromArrayOrDefault(string $arrayKey, array $array, bool $dftVal = false): bool
    {
        if (key_exists($arrayKey, $array)) {
            return $array[$arrayKey];
        }
        return $dftVal;
    }

    public static function getEnvFile(string $filePath)
    {

        //define("APP_ENV", "DEV");

        $charSep = "/";
        if (StringUtils::indexOf($filePath, $charSep) == -1) {
            $charSep = "\\";
        }

        $pathSegments = explode($charSep, $filePath);
        $fileName = array_pop($pathSegments);

        $filenameSegments = [$fileName];
        if (StringUtils::indexOf($fileName, ".")) {
            $filenameSegments = explode(".", $fileName);
        }

        $newfileName = $filenameSegments[0] . "_" . APP_ENV;
        if (count($filenameSegments) > 1) {
            for ($i = 1; $i < count($filenameSegments); $i++) {
                $newfileName .= "." . $filenameSegments[$i];
            }
        }

        $pathSegments[] = $newfileName;

        return implode($charSep, $pathSegments);

    }

}