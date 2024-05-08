<?php

namespace myphpfw\core\utils;

use myphpfw\core\App;
use myphpfw\core\MyPhpFwConf;
use myphpfw\core\obj\UrlActionRes;

class RoutesUtils
{

    /**
     * Hydrate les détails de la route à partir de l'action URL
     *
     * Cette méthode statique est utilisée pour hydrater les détails de la route à partir de l'action URL.
     * Elle utilise des expressions régulières pour extraire les variables de l'URL et les stocke dans l'objet de détails de la route.
     *
     * @param UrlActionRes &$routeDetailsToHydrate L'objet de détails de la route à hydrater.
     * @param string $routeName Le nom de la route.
     * @param string $urlAction L'action URL.
     * @param callable $routeActionFn La fonction d'action de la route.
     * @return void
     */
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

    /**
     * Détermine si une route est connue
     * @param string $routeName
     * @param bool $isSearchGetRoutes
     * @param bool $isSearchPostRoutes
     * @return bool
     */
    public static function isKnownRoute(string $routeName, bool $isSearchGetRoutes = true, bool $isSearchPostRoutes = false) : bool {
        if ($isSearchGetRoutes && key_exists($routeName, App::$RoutesGet)) {
            return true;
        } else if ($isSearchPostRoutes && key_exists($routeName, App::$RoutesPost)) {
            return true;
        }
        return false;

    }

    /**
     * Obtient l'URL de la route
     *
     * Cette méthode statique est utilisée pour obtenir l'URL de la route.
     * Elle lance une exception si la route est inconnue.
     *
     * @param string $routeName Le nom de la route.
     * @param array $routeParams Les paramètres de la route.
     * @param array $urlArgs Les arguments de l'URL.
     * @return string L'URL de la route.
     * @throws \Exception Si la route est inconnue.
     */
    public static function getRouteUrlNoArray(string $routeName, string ...$routeParams)
    {
        if (count($routeParams) % 2 != 0) {
            throw new \Exception('getRouteUrlNoArray : parametre $routeParams doit etre pair : clef => valeur');
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
     * Obtient l'URL de la route
     *
     * Cette méthode statique est utilisée pour obtenir l'URL de la route.
     * Elle lance une exception si la route est inconnue.
     *
     * @param string $routeName Le nom de la route.
     * @param array $routeParams Les paramètres de la route.
     * @param array $urlArgs Les arguments de l'URL.
     * @return string L'URL de la route.
     * @throws \Exception Si la route est inconnue.
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

        $mainUrl = MyPhpFwConf::$URL_ROOT . "/" . MyPhpFwConf::$SITE_PREFIX_URL . $routeName ;
        if (strpos($mainUrl, "?", strlen(MyPhpFwConf::$URL_ROOT)) > -1) {
            $mainUrl .= '&' . $strUrlArgs;
        } else {
            $mainUrl .= '?' .$strUrlArgs;
        }
        return $mainUrl;
    }

}