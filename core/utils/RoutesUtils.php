<?php

namespace myphpfw\core\utils;

use myphpfw\core\App;
use myphpfw\core\obj\UrlActionRes;

class RoutesUtils
{

    public static function hydrateRouteDetailsFromUrlAction(UrlActionRes &$routeDetailsToHydrate, $routeName, $urlAction, $routeActionFn)
    {
        if (0 !== preg_match_all('/(?\'var\'{{(?\'nameVar\'\w+?)}})/m', $routeName, $matches, PREG_SET_ORDER, 0)) {

            $pattern = $routeName;

            $varsName = [];
            foreach ($matches as $m) {
                $pattern = preg_replace("/" . $m["var"] . "/m", "(?'" . $m["nameVar"] . "'[^/]*?)", $pattern);
                $varsName[] = $m['nameVar'];
            }
            //var_dump("M", $pattern);
            $pattern = str_replace("/", "\/", $pattern);
            if (0 !== preg_match_all('/^' . $pattern . '$/m', $urlAction, $matches, PREG_SET_ORDER, 0)) {
                $routeDetailsToHydrate->setIsValidAction(true);
                $routeDetailsToHydrate->setRouteName($routeName);
                $routeDetailsToHydrate->setRouteAction($routeActionFn);

                foreach ($varsName as $var) {
                    $routeDetailsToHydrate->getVarsUrlByRef()[$var] = $matches[0][$var];
                }

                //break;
            }
        } else if ($routeName == $urlAction) {
            $routeDetailsToHydrate->setIsValidAction(true);
            $routeDetailsToHydrate->setRouteName($routeName);
            $routeDetailsToHydrate->setRouteAction($routeActionFn);
            //break;
        }

    }

    public static function getRouteUrlNoArray(string $routeName, string ...$routeParams)
    {
        if (count($routeParams) % 2 != 0) {
            throw new \Exception("getRouteUrlNoArray : parametre $routeParams doit etre pair : clef => valeur");
        }
        $routeParamsArgs = [];
        $key = null;
        foreach ($routeParams as $r) {
            if ($key == null) {
                $key = $r;
            } else {
                $routeParamsArgs[$key] = $r;
                $key = null;
            }
        }

        return self::getRouteUrl($routeName, $routeParamsArgs);
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return string
     * @throws \Exception
     */
    public static function getRouteUrl(string $routeName, array $routeParams = [], array $urlArgs = [])
    {

        $route = null;
        if (key_exists($routeName, App::$RoutesGet)) {
            $route = App::$RoutesGet[$routeName];
        } else if (key_exists($routeName, App::$RoutesPost)) {
            $route = App::$RoutesPost[$routeName];
        }

        if (App::$sessionObj != null && App::$sessionObj->isConnected()) {
            $routeParams['api_key'] = App::$sessionObj->userConnexion()->getUserApiKey();
        }

        if ($route == null) {
            throw new \Exception("getRouteUrl() : route '$routeName' inconnue");
        }

        foreach ($routeParams as $key => $val) {
            $routeName = str_replace("{{" . $key . "}}", $val, $routeName);
        }

        $strUrlArgs ="";
        if (count($urlArgs) > 0) {
            $tmpUrlArgs = [];
            foreach ($urlArgs as $key => $val) {
                $tmpUrlArgs[] = sprintf('%s=%s', urlencode($key), urlencode($val));
            }
            $strUrlArgs = implode('&', $tmpUrlArgs);
        }

        $mainUrl = URL_ROOT . "/" . SITE_PREFIX_URL . $routeName ;
        if (strpos($mainUrl, "?", strlen(URL_ROOT)) > -1) {
            $mainUrl .= '&' . $strUrlArgs;
        } else {
            $mainUrl .= '?' .$strUrlArgs;
        }
        return $mainUrl;
    }

}