<?php

namespace myphpfw\core\obj\session;

use myphpfw\core\utils\lang\ArrayUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\Utils;

class SessionObjFormSecurity
{
    use SessionObjUtilsTrait;


    public function __construct(string $varStart)
    {
        $this->prefix = $varStart;

        if (empty($this->securityCode)) {
            $this->securityCode = StringUtils::generateRandomString(8);
        }

        $f = $this->formCodes;
        if (!is_array($f)) {
            $this->formCodes = [];
        }
    }

    public function newFormCode($formName) : string {
        $code = $this->securityCode . StringUtils::generateRandomString(24);
        $this->formCodes[$formName] = password_hash($code, PASSWORD_DEFAULT);

        return $code;
    }

    public function verifyCode($formName, $code) : bool {

        $codeOrig = Utils::getStringFromArrayOrDefault($formName, $this->formCodes);
        if ($code == null) return false;
        $isOk = password_verify($code, $codeOrig);
        if($isOk) {
            // TODO à décommenter
           // unset($this->formCodes[$formName]);
        }
        return $isOk;
    }
}