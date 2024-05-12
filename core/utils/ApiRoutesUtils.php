<?php

namespace myphpfw\core\utils;

use Doctrine\ORM\EntityManager;
use myphpfw\core\annotation\ApiRoute;
use myphpfw\core\annotation\RouteParamData;
use myphpfw\core\obj\ResponseHttp;
use myphpfw\core\utils\lang\ReflectionUtils;
use php\app\modules\api\ApiReceiver;

class ApiRoutesUtils
{

    private static array $cacheGetApiSignToJsObjProps = [];

    private static array $cacheAnnotationRoutes = [];

    public static function hydrateApiRoutesFromClass(array &$RoutesGet, array &$RoutesPost, EntityManager $entityManager, string $className): void
    {

        $methods = ReflectionUtils::getMethods($className);
        foreach ($methods as $method) {

            $methodName = $method->getName();
            if ($methodName == "__construct") {
                continue;
            }

            /** @var ApiRoute $apiRouteAnnotation */
            $apiRouteAnnotation = ReflectionUtils::getAnnotationOnMethod($className, $methodName, ApiRoute::class);
            if ($apiRouteAnnotation == null) {
                continue;
            }

            $paramsArray = [
                "route" => $apiRouteAnnotation,
                "params" => []
            ];

            /** @var RouteParamData[] $paramsAnnotation */
            $paramsAnnotation = ReflectionUtils::getAnnotationsOnMethod($className, $methodName, RouteParamData::class);
            foreach ($paramsAnnotation as $param) {
                if ($param->getParamName() == null) {
                    throw new \Exception("RouteParamData: param name est obligatoire");
                }
                $paramsArray['params'][] = $param;
            }

            if ($apiRouteAnnotation->getMethod() == "GET") {
                $RoutesGet[$apiRouteAnnotation->getRoutePath()] = function (string $api_key) use ($entityManager, $className, $methodName, $apiRouteAnnotation): ResponseHttp {
                    return (new ApiReceiver($entityManager))->routeAndRespondsAlt(
                        $apiRouteAnnotation->getRoutePath(),
                        $api_key,
                        $className,
                        $methodName
                    );
                };
            } else if ($apiRouteAnnotation->getMethod() == "POST") {
                $RoutesPost[$apiRouteAnnotation->getRoutePath()] = function (string $api_key) use ($entityManager, $className, $methodName, $apiRouteAnnotation): ResponseHttp {
                    return (new ApiReceiver($entityManager))->routeAndRespondsAlt(
                        $apiRouteAnnotation->getRoutePath(),
                        $api_key,
                        $className,
                        $methodName
                    );
                };
            }

            self::$cacheAnnotationRoutes[$apiRouteAnnotation->getMethod().'#'.$className . $methodName] = $paramsArray;
        }
    }

    public static function getApiSign(string $className, string $methodName, string $httpMethod = 'POST'): string
    {
        $key = $httpMethod . '#' . $className . $methodName;
        if (!key_exists($key, self::$cacheAnnotationRoutes)) {
            throw new \Exception("getApiSign() : method pour la clef '$key' inconnue");
        }

        $result = [];
        $paramsArray = self::$cacheAnnotationRoutes[$key]['params'];
        foreach ($paramsArray as $param) {
            $result[] = $param->getParamName();
        }

        return implode(", ", $result);
    }

    public static function getRoute(string $className, string $methodName, string $userApiKey, string $httpMethod = 'POST') {
        $key = $httpMethod . '#' . $className . $methodName;
        if (!key_exists($key, self::$cacheAnnotationRoutes)) {
            throw new \Exception("getRoute() : method pour la clef '$key' inconnue");
        }

        $route = self::$cacheAnnotationRoutes[$key]['route']->getRoutePath();
        return RoutesUtils::getRouteUrlNoArray($route, 'api_key', $userApiKey);
    }

    public static function getApiSignToJsObjProps($className, $methodName, $httpMethod = 'POST'): string
    {
        $key = $httpMethod . '#' . $className . $methodName;
        if (!key_exists($key, self::$cacheAnnotationRoutes)) {
           throw new \Exception("getApiSignToJsObjProps() : method pour la clef '$key' inconnue");
        }

        $result = [];
        $paramsArray = self::$cacheAnnotationRoutes[$key]['params'];
        foreach ($paramsArray as $param) {
            $result[] = $param->getParamName() . " : " . $param->getParamName();
        }


        return implode(", ", $result);
    }
}