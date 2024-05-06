<?php

namespace myphpfw\core\module\login;

use Doctrine\ORM\EntityManager;
use myphpfw\core\App;
use myphpfw\core\module\IUserFw;
use myphpfw\core\MyPhpFwConf;
use myphpfw\core\obj\session\SessionObj;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\Utils;


abstract class AbstractLoginControler
{

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    protected function loggoff(): void
    {
        try {
            App::$sessionObj->userConnexion()->disconnect();
        } catch (\Exception $ex) {
            Utils::logError("logoff", $ex);
        }

        setcookie("siteConnected", "", -1);


    }

    protected function tryReconnectByStringValue(string $stringValue): bool
    {
        try {
            $strJsonDecrypted = StringUtils::decryptWithXor($stringValue, MyPhpFwConf::$SITE_KEY);
            $arr = json_decode($strJsonDecrypted);
            $currentPcId = $this->getComputerId();
            $potUserName = $arr[0];
            $potPwd = $arr[1];
            $potPcId = $arr[2];
            //$potUserName = substr($potUserName,0, -64);
            /** @var IUserFw $user */
            $user = $this->entityManager
                ->getRepository(MyPhpFwConf::$USER_CLASS)
                ->findOneBy(["nom" => $potUserName]);
            if ($potPcId == $currentPcId && $user != null) {


                return $this->tryDoConnexion(
                    $potUserName,
                    $potPwd,
                    true,
                    MyPhpFwConf::$USER_USERNAME_FIELD_NAME


                );
            }
        } catch (\Exception $ex) {
            SessionObj::resetSession();
            setcookie("siteConnected", "", -1);
            Utils::logWarn("Erreur lors de la reconnexion", $ex);
        }

        return false;
    }

    private function getComputerId(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Tente de connecter l'utilisateur
     *
     * @param string $formUsername Le nom d'utilisateur
     * @param string $formPwd Le mot de passe
     * @param bool $isRmberMe Si l'utilisateur a coché la case "Se souvenir de moi"
     * @return bool Vrai si la connexion a réussi, faux sinon
     * @throws \Doctrine\ORM\Exception\NotSupported
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function tryDoConnexion(string $formUsername,
                                      string $formPwd,
                                      bool   $isRmberMe,
                                        string $fieldName = "username",
                                        string &$errorMsg = null

    ): bool
    {

        // On récupère l'utilisateur potentiel
        /** @var IUserFw $user */
        $user = $this->entityManager
            ->getRepository(MyPhpFwConf::$USER_CLASS)
            ->findOneBy([$fieldName => $formUsername]);

        // Si l'utilisateur n'existe pas ou que le mot de passe est incorrect
        if ($user == null || !password_verify($formPwd, $user->getPassword())) {
            return false;
        }

        // On connecte l'utilisateur
        $user->setPassword(password_hash($formPwd, CRYPT_BLOWFISH));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userCon = App::$sessionObj->initUserConnexion();
        App::$sessionObj->setIsConnected(true);
        $userCon->setUserId($user->getId());
        $userCon->setUserApiKey($user->getUserApiToken());

        // On enregistre l'utilisateur dans un cookie si demandé
        if ($isRmberMe) {
            $current_time = time();
            $cookie_expiration_time = $current_time + (30 * 24 * 60 * 60);
            $arr = [$formUsername, $formPwd, $this->getComputerId()];
            setcookie("siteConnected",
                StringUtils::encryptWithXor(
                    json_encode($arr), MyPhpFwConf::$SITE_KEY),
                $cookie_expiration_time);
        }

        return true;

    }

    protected function tryRegisterNewUser(string $formUsername, string $formPwd, string $fieldName = "username", string &$errorMsg): bool
    {
        // On vérifie que l'utilisateur n'existe pas déjà
        $user = $this->entityManager
            ->getRepository(MyPhpFwConf::$USER_CLASS)
            ->findOneBy([$fieldName => $formUsername]);
        if ($user != null) {
            $errorMsg = "Cet utilisateur existe déjà";
            return false;
        }

        // On crée l'utilisateur
        $user = new MyPhpFwConf::$USER_CLASS();
        /** @var IUserFw $user */
        $user
            ->setUsername($formUsername)
            ->setPassword(password_hash($formPwd, CRYPT_BLOWFISH))
            ->setUserApiToken(StringUtils::generateRandomString(10));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $errorMsg = null;
        return true;
    }


}
