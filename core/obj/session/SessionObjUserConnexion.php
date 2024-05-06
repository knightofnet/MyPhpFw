<?php

namespace myphpfw\core\obj\session;

use Doctrine\ORM\EntityManager;
use myphpfw\core\MyPhpFwConf;
use myphpfw\core\utils\lang\ReflectionUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\Utils;

class SessionObjUserConnexion
{
    use SessionObjUtilsTrait;


    public function __construct(string $varStart)
    {
        $this->prefix = $varStart;
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
        // Utils::getStringFromArrayOrDefault($this->varStart."userId", $_SESSION);
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
        //$_SESSION[$this->varStart."userId"] = $userId;
        //$this->$$this->varStart."userId" = $userId;
    }


    public function getUserApiKey() : string {
        return $this->userApiKey;
    }
    public function setUserApiKey(string $userApiKey) : void
    {
        $this->userApiKey = $userApiKey;
    }



    public function getUser(EntityManager $em)
    {
        if (empty($this->getUserId()) || empty($this->getUserApiKey())) {
            $this->disconnect();
            return null;
        }


        $ret = $em->getRepository(MyPhpFwConf::$USER_CLASS)->findOneBy(["id" => $this->getUserId(), MyPhpFwConf::$USER_USERAPITOKEN_FIELD_NAME => $this->getUserApiKey()]);
        if ($ret == null) {
            $this->disconnect();
        }
        return $ret;

    }

    public function restoreUserConnexion()
    {
    }

    public function disconnect()
    {
        $this->userId = -1;
        $this->isConnected = false;

        session_destroy();

        session_name(MyPhpFwConf::$INNER_SITE_NAME);
        session_start();

    }




}