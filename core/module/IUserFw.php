<?php

namespace myphpfw\core\module;

interface IUserFw
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return IUserFw
     */
    public function setId($id);

   
    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @param mixed $password
     * @return IUserFw
     */
    public function setPassword($password) : IUserFw;

    /**
     * @return string
     */
    public function getUserApiToken() : string;

    /**
     * @param string $userApiToken
     * @return IUserFw
     */
    public function setUserApiToken(string $userApiToken): IUserFw;
}