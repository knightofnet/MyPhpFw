<?php

namespace myphpfw\core\obj\session;

use myphpfw\core\MyPhpFwConf;
use myphpfw\core\utils\Utils;


class SessionObj
{

    use SessionObjUtilsTrait;
    private ?SessionObjUserConnexion $userConnexion = null;
    private SessionObjFormSecurity $formSecurity;
    private SessionObjMessagesApp $msgApp;

    public function __construct()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            throw new \Exception("Session is not active here");
        }

        $this->formSecurity = new SessionObjFormSecurity("_formSecurity_");
        $this->msgApp = new SessionObjMessagesApp("_msgApp_");
        if ($this->isConnected()) {
            $this->userConnexion = $this->initUserConnexion();
        }


    }

    public function setMethod(string $method): void
    {
        //$_SESSION["method"] = $method;
        $this->method = $method;
    }

    public function getMethod() : ?string {
        return $this->method;
        //Utils::getStringFromArrayOrDefault("method", $_SESSION);
    }

    public function &getHistory() : array {
        if ($this->history == null) {
            $this->history = [];
        }
        return $this->history;
    }

    public function setHistory(array $arr) {
        $this->history = $arr;
    }

    public function initUserConnexion() : SessionObjUserConnexion {

        //$_SESSION["userConn_isConnected"] = true;
        $userConnexion = new SessionObjUserConnexion("_userConn_");
        return $userConnexion;
    }

    public function isConnected(): bool
    {
        $vKey= "userConn_isConnected";
        $ret = $this->$vKey;

        if ($ret == null) return false;
        return true;
        //Utils::getBoolFromArrayOrDefault("userConn_isConnected", $_SESSION);
    }

    public function setIsConnected(bool $isconnected) : void {
        $vKey= "userConn_isConnected";
        $this->$vKey = $isconnected;
    }

    /**
     * @return SessionObjUserConnexion
     */
    public function userConnexion(): SessionObjUserConnexion
    {
        if (!$this->isConnected()) {
            throw new \Exception("Session user non initialisée");
        };

        if ($this->userConnexion == null) {
            $this->userConnexion = $this->initUserConnexion();
            $this->userConnexion->restoreUserConnexion();

        }
        return $this->userConnexion;
    }



    /**
     * @return SessionObjFormSecurity
     */
    public function formSecurity(): SessionObjFormSecurity
    {
        return $this->formSecurity;
    }

    /**
     * @return SessionObjMessagesApp
     */
    public function msgApp(): SessionObjMessagesApp
    {
        return $this->msgApp;
    }

    public function getSessionUniqueCode()
    {
        return session_id();
    }

    public static function resetSession() : void {
        session_name(MyPhpFwConf::$INNER_SITE_NAME);
        session_start();
    }


}