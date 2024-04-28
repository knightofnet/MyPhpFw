<?php

namespace myphpfw\core\obj\session;

use myphpfw\core\utils\lang\ArrayUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\Utils;

class SessionObjMessagesApp
{
    use SessionObjUtilsTrait;


    const MSG_INFO = "info";

    public function __construct(string $varStart)
    {
        $this->prefix = $varStart;

        $f = $this->msgArray;
        if (!is_array($f)) {
            $this->msgArray = [];
        }
    }

    public function addMsg(string $msg, string $msgType=self::MSG_INFO) : void {
        $this->msgArray[] = ["msg" => $msg, "type" => $msgType];
    }

    public function count() : int {
        return count($this->msgArray);
    }

    public function getAll(bool $getAndClear = true) : array {
        $arrMsg = $this->msgArray;
        if ($getAndClear) {
            $this->clear();
        }
        return $arrMsg;
    }

    public function clear() : void {
        $this->msgArray = [];
    }


}