<?php

namespace myphpfw\core\module\login;

use Doctrine\ORM\EntityManager;
use eftec\bladeone\BladeOne;
use php\app\modules\common\entities\User;
use php\app\modules\common\MainTplBladeObj;
use php\app\Runner;
use myphpfw\core\App;
use myphpfw\core\obj\JsonReturnObj;
use myphpfw\core\obj\ResponseHttp;
use myphpfw\core\obj\session\SessionObj;
use myphpfw\core\utils\lang\ArrayUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\RoutesUtils;
use myphpfw\core\utils\TemplateEngine;
use myphpfw\core\utils\Utils;

class LoginControler
{

    private EntityManager $entityManager;
    private BladeOne $bladeOne;


    protected array $tplVars = [];

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->bladeOne = new BladeOne(SVR_ROOT . "/view/b", BLADE_COMPILED_PATH, IS_DEBUG ? BladeOne::MODE_DEBUG : BladeOne::MODE_AUTO);;

        $this->bladeOne->directive("getRouteUrl", [RoutesUtils::class, "getRouteUrlNoArray"]);
        $this->bladeOne->pipeEnable = true;
    }

    public function logoffAndshowLoginForm(): ResponseHttp
    {
        try {
            App::$sessionObj->userConnexion()->disconnect();
        } catch (\Exception $ex) {
            Utils::logError("logoff", $ex);
        }

        setcookie("siteConnected", "", -1);


        return ResponseHttp::RedirectTo(RoutesUtils::getRouteUrl(Runner::ROUTE_LOGIN));

    }

    public function showLoginForm(): ResponseHttp
    {
        error_reporting(E_ALL ^ E_NOTICE);
        $referrer = ArrayUtils::tryGet("HTTP_REFERER", $_SERVER);

        $cookieIfExists = ArrayUtils::tryGet("siteConnected", $_COOKIE);
        if ($cookieIfExists != null) {
            try {
                $strJsonDecrypted = StringUtils::decryptWithXor($cookieIfExists, SITE_KEY);
                $arr = json_decode($strJsonDecrypted);
                $currentPcId = $this->getComputerId();
                $potUserName = $arr[0];
                $potPwd = $arr[1];
                $potPcId = $arr[2];
                //$potUserName = substr($potUserName,0, -64);
                /** @var User $user */
                $user = $this->entityManager
                    ->getRepository(User::class)
                    ->findOneBy(["nom" => $potUserName]);
                if ($potPcId == $currentPcId && $user != null) {
                    $routeToRedirect = Runner::ROUTE_HOME;
                    if (key_exists('redirect', $_GET)) {
                        $routeToRedirect = urldecode($_GET['redirect']);
                    }

                    return $this->doConnexionOrRegister(
                        $potUserName,
                        $potPwd,
                        true,
                        $routeToRedirect
                    );
                }
            } catch(\Exception $ex ) {
                SessionObj::resetSession();
                setcookie("siteConnected", "", -1);
                Utils::logWarn("Erreur lors de la reconnexion", $ex);
            }
        }

        $this->tplVars["title"] = "Login";
        $this->tplVars["urlRoot"] = URL_ROOT;
        $this->tplVars["ref_url"] = $referrer != null ? base64_encode($referrer) : "";
        $this->tplVars["valueLogin"] = ArrayUtils::tryGet("idUser", $_GET, "");

        return ResponseHttp::ResultsBlade($this->getBladeOne(),
            "public_login",
            MainTplBladeObj::getBladeVars($this->tplVars));
    }

    public function doConnexionOrRegister(string $formUsername,
                                          string $formPwd,
                                          bool $isRmberMe,
                                          string $routeToRedirect
    ): ResponseHttp
    {

        // connexion

        /** @var User $user */
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(["nom" => $formUsername]);
        if ($user == null) {
            return ResponseHttp::RedirectTo(RoutesUtils::getRouteUrl($routeToRedirect));
        }

        if (password_verify($formPwd, $user->getMotDePasse())) {

            $user->setMotDePasse(password_hash($formPwd, CRYPT_BLOWFISH));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userCon = App::$sessionObj->initUserConnexion();
            App::$sessionObj->setIsConnected(true);
            $userCon->setUserId($user->getId());
            $userCon->setUserApiKey($user->getUserApiToken());

            if ($isRmberMe) {
                $current_time = time();
                $cookie_expiration_time = $current_time + (30 * 24 * 60 * 60);
                $arr = [$formUsername, $formPwd, $this->getComputerId()];
                setcookie("siteConnected",
                    StringUtils::encryptWithXor(
                        json_encode($arr), SITE_KEY),
                    $cookie_expiration_time);
            }
        }

        if (key_exists('redirect', $_GET)) {
            $routeToRedirect = urldecode($_GET['redirect']);
        }

        return ResponseHttp::RedirectTo(RoutesUtils::getRouteUrl($routeToRedirect));

    }

    private function getComputerId() : string {
        return $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']   ;
    }

    public function getBladeOne(): BladeOne
    {
        return $this->bladeOne;
    }


}
