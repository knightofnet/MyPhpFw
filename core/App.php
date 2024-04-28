<?php

namespace php\core;

use ArgumentCountError;
use Closure as ClosureAlias;
use Error;
use php\core\obj\JsonReturnObj;
use php\core\obj\ReflectionToCallMethod;
use php\core\obj\ResponseHttp;
use php\core\obj\session\SessionObj;
use php\core\obj\UrlActionRes;
use php\core\utils\lang\ArrayUtils;
use php\core\utils\Results;
use php\core\utils\RoutesUtils;
use php\core\utils\Utils;
use PhpParser\Node\Expr\Closure;
use ReflectionFunction;
use ReflectionProperty;
use Throwable;

class App
{


    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public static array $appClosureArgs = [];
    public static ?string $currentRoute = null;

    private static string $appVerb = "action";
    public static array $RoutesGet = [];
    public static array $RoutesPost = [];
    public static array $taskCronToDo = [];

    public static ?SessionObj $sessionObj = null;

    public static function run(string $verb = "action")
    {
        self::$appVerb = $verb;


        if (!isset($_GET) || count($_GET) == 0 || empty($_GET[self::$appVerb])) {
            if (!key_exists("", self::$RoutesGet)) {
                Results::result404();
                die(1);
            }
        }


        try {
            Utils::rotateLogFile(dirname($_SERVER['DOCUMENT_ROOT']) . '/'.LOG_FILENAME);

            $method = $_SERVER['REQUEST_METHOD'];
            $action = self::getAction($method);

            if ($action === null) {
                throw new \InvalidArgumentException("Aucune action définie");
            }

            session_name(INNER_SITE_NAME);
            session_start();
            self::$sessionObj = new SessionObj();
            self::$sessionObj->setMethod($method);

            if (self::METHOD_GET == $method) {
                // GET
                Utils::logDebug("GET :", $_GET);
                $routeDetails = self::key_exists_action($action, self::$RoutesGet);
                if ($routeDetails->isValidAction()) {
                    self::doGet($routeDetails, $action);
                }
            } else if (self::METHOD_POST == $method) {
                // POST
                $routeDetails = self::key_exists_action($action, self::$RoutesPost);
                if ($routeDetails->isValidAction()) {

                    self::doPost($routeDetails, $action);
                }
            }
        } catch (Error $e) {
            self::catchErrorAndException($e);
        } catch (\Exception $e) {
            self::catchErrorAndException($e);
        }
    }

    /**
     * @param ClosureAlias $c
     * @param $action
     * @return array
     * @throws \ReflectionException
     */
    public static function getClosureArgsFromGetParams(ClosureAlias $c, $action): array
    {
        $closure_args = [];

        $ref = new ReflectionFunction($c);
        $getArray = $_GET;
        if (!isset($_GET) || count($_GET) == 0 || empty($_GET[self::$appVerb])) {
            if (key_exists("", self::$RoutesGet)) {
                $getArray = [self::$appVerb => ""];
            }
        }
        Utils::logDebug("getArray", $_GET);
        if (!empty(SITE_PREFIX_URL)) {
            preg_match_all('/([^?=&]+)(=([^&]*))/', $getArray[self::$appVerb], $matches);
            $params = array();
            for ($i = 0; $i < count($matches[1]); $i++) {
                $params[$matches[1][$i]] = $matches[3][$i];
            }
            $getArray = $params;
        }
        Utils::logDebug("getArrayNext", $getArray);

        unset($getArray[self::$appVerb]);
        foreach ($ref->getParameters() as $param) {
            if (!$param->hasType()) {
                throw new \InvalidArgumentException(sprintf(self::$appVerb . " '%s' : argument '%s' doesnt have a type", $action, $param->name));
            }
            //var_dump($param, $param->hasType(), $param->getType()->getName());

            if (key_exists($param->name, $getArray)) {
                $closure_args[$param->name] = $getArray[$param->name];
            }
        }
        return $closure_args;
    }

    private static function getClosureArgsFromPostParams($c, $action): array
    {
        $retArgs = [];

        try {
            $ref = new ReflectionFunction($c);
            $postArray = $_POST;
            foreach ($ref->getParameters() as $param) {
                if (!$param->hasType()) {
                    throw new \InvalidArgumentException(sprintf(self::$appVerb . " '%s' : argument '%s' doesnt have a type", $action, $param->name));
                }

                if ($param->getType() != null && !$param->getType()->isBuiltin()) {
                    $obj = self::hydrateObject($param->getType(), $postArray);
                    if ($obj != null) {
                        $retArgs[$param->name] = $obj;
                    }
                } else if (key_exists($param->name, $postArray)) {
                    $retArgs[$param->name] = $postArray[$param->name];
                }
            }
        } catch (Error $e) {
            Utils::logError("getClosureArgsFromPostParams :", $e->getMessage());
            throw $e;
        }
        return $retArgs;
    }

    private static function hydrateObject(\ReflectionNamedType $type, array $postArray)
    {
        //var_dump($type->getName());
        $r = new \ReflectionClass($type->getName());
        if ($r->getConstructor() != null) {
            $retObj = $r->newInstance($type->getName());
        } else {
            $retObj = $r->newInstanceWithoutConstructor($type->getName());
        }

        $canHydrate = true;
        $mapGetterNameByPropName = [];
        foreach ($r->getProperties() as $prop) {
            $propName = $prop->getName();
            $isPublicProp = $prop->getModifiers() == ReflectionProperty::IS_PUBLIC;

            if (!key_exists($propName, $postArray)) {
                continue;
            }

            $toCallMethod = new ReflectionToCallMethod();
            $toCallMethod->setPropName($propName);
            if ($isPublicProp) {

                $toCallMethod->setType("closure");
                $toCallMethod->setClosure(function ($val) use ($prop, $retObj) {
                    $prop->setValue($retObj, $val);
                });

                if (in_array($prop->getType()->getName(), ["string", "float", "bool", "int"])) {
                    $toCallMethod->setInnerType($prop->getType()->getName());
                    $mapGetterNameByPropName[$propName] = $toCallMethod;
                }
            } else {

                $toCallMethod->setType("setter");
                $getterName = "set" . ucfirst($propName);
                if ($r->hasMethod($getterName)) {
                    $met = $r->getMethod($getterName);
                    if ($met->getNumberOfParameters() == 1) {
                        $toCallMethod->setReflectionMethod($met);
                        $firstParamType = $met->getParameters()[0]->getType();

                        if (in_array($firstParamType->getName(), ["string", "float", "bool", "int"])) {
                            $toCallMethod->setInnerType($firstParamType->getName());
                            $mapGetterNameByPropName[$propName] = $toCallMethod;
                        }
                    }

                }
            }
        }

        if ($canHydrate) {
            foreach ($r->getProperties() as $prop) {
                $propName = $prop->getName();
                if (key_exists($propName, $mapGetterNameByPropName)) {
                    $tcm = $mapGetterNameByPropName[$propName];

                    $valRaw = $postArray[$prop->getName()];
                    $val = $valRaw;
                    if ($tcm->getInnerType() == "int") {
                        $val = intval($valRaw);
                    }

                    if ("closure" == $tcm->getType()) {
                        call_user_func($tcm->getClosure(), $val);
                    } else if ("setter" == $tcm->getType()) {
                        $tcm->getReflectionMethod()
                            ->invoke($retObj, $val);
                    }

                }
                //$prop->setValue($retObj, $postArray[$prop->getName()]);

            }

            return $retObj;
        }

        return null;


    }

    private static function getAction(string $method)
    {

        if (isset($_GET[self::$appVerb])) {
            return $_GET[self::$appVerb];
        }

        if ((self::METHOD_GET == $method && key_exists("", self::$RoutesGet))
            || (self::METHOD_POST == $method && key_exists("", self::$RoutesPost))) {
            return "";
        }

        return null;
    }

    /**
     * @param $action
     * @return mixed
     */
    public static function doGet(UrlActionRes $routeDetails, $action)
    {
        $c = $routeDetails->getRouteAction();
        //$ref = new ReflectionFunction($c);

        $closure_args = $routeDetails->getVarsUrl();
        $closure_args = array_merge($closure_args, self::getClosureArgsFromGetParams($c, $action));
        self::endTrtCall($c, $closure_args, $action);
        //return $e;
    }

    /**
     * @param $action
     * @return void
     * @throws \ReflectionException
     */
    public static function doPost(UrlActionRes $routeDetails, $action)
    {
        $c = $routeDetails->getRouteAction();
        //$ref = new ReflectionFunction($c);

        $closure_args = $routeDetails->getVarsUrl();
        $closure_args = array_merge($closure_args, self::getClosureArgsFromGetParams($c, $action));
        $closure_args = array_merge($closure_args, self::getClosureArgsFromPostParams($c, $action));

        self::endTrtCall($c, $closure_args, $action);

    }

    /**
     * @param $c
     * @param array $closure_args
     * @param $action
     * @return void
     */
    public static function endTrtCall($c, array $closure_args, $action): void
    {
        self::$currentRoute = $action;

        try {
            self::$appClosureArgs = $closure_args;
            $closure_args = self::reorderArgsArrayForClosure($c, $closure_args);
            $res = call_user_func_array($c, $closure_args);
            if (is_string($res)) {
                echo $res;
            } else if ($res instanceof ResponseHttp) {
                $res->doResponse();
            }

        } catch (ArgumentCountError $e) {
            throw new ArgumentCountError(sprintf(self::$appVerb . " '%s' : pas assez de paramètre fournis", $action), 0, $e);
        }
    }

    private static function key_exists_action($action, array $routes): UrlActionRes
    {
        $retV = new UrlActionRes();

        $delimiterPosition = strrpos($action, "?");
        if ($delimiterPosition !== false) {
            $action = substr($action, 0, $delimiterPosition);
        }
        $ix = 0;
        while (!$retV->isValidAction() && $ix < count($routes)) {
            $route = array_keys($routes)[$ix++];
            //foreach ($routes as $route => $c) {
            RoutesUtils::hydrateRouteDetailsFromUrlAction($retV, $route, $action, $routes[$route]);

            //var_dump($matches);
        }
        //var_dump($retV);
        return $retV;
    }

    private static function reorderArgsArrayForClosure(ClosureAlias $closure, array $closure_args): array
    {
        $retArray = [];
        $ref = new ReflectionFunction($closure);

        foreach ($ref->getParameters() as $param) {
            if (key_exists($param->name, $closure_args)) {
                $retArray[] = $closure_args[$param->name];
            } else {
                if ($param->isDefaultValueAvailable()) {
                    $retArray[] = $param->getDefaultValue();
                }
            }
        }

        return $retArray;
    }

    /**
     * @param $e
     * @return void
     * @throws \Throwable
     */
    public static function catchErrorAndException(Throwable $e): void
    {
        http_response_code(500);

        /*
        if ($e instanceof Error) {
            Utils::logError("Erreur fatale : ");
            $errorMessage = sprintf(
                "Error: %s in %s on line %d",
                "",
                $e->getFile(),
                $e->getLine()
            );
            error_log($errorMessage, 3, dirname($_SERVER['DOCUMENT_ROOT']) . '/'.LOG_FILENAME);
        } else {
        */
        Utils::logError("Erreur(Exception) fatale : ", $e->getMessage(), $e->getFile(), $e->getLine(), ArrayUtils::take($e->getTrace(), 15));

        $prev = $e->getPrevious();
        while ($prev != null) {
            Utils::logError(" Previous error : ", $prev->getMessage(), $prev->getFile(), $prev->getLine(), ArrayUtils::take($prev->getTrace(), 15));
            $prev = $prev->getPrevious();

        }
        if (IS_DEBUG) {
            throw $e;
        }
        /*
    }
        */


        $retJson = new JsonReturnObj();
        $retJson->setState("ko");

        Utils::logError("Erreur fatale : ", $e);
        $retJson->addAlert("Erreur inattendue");
        $retJson->addDebug($e);


        echo json_encode($retJson->toArray());
        die(1);
    }


}