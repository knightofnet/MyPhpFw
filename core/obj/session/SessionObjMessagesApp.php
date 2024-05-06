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

    /**
     * Retourn le premier message et le supprime de la liste si $getAndClear est true
     * @param bool $getAndClear
     * @return array|null
     */
    public function getFirst(bool $getAndClear = true) : ?array {
        $arrMsg = $this->msgArray;

        $first = ArrayUtils::first($arrMsg);
        if ($first == null) { return null; }

        if ($getAndClear) {
            $this->msgArray = ArrayUtils::skip($arrMsg, 1);
        }
        return $first;
    }

    public function clear() : void {
        $this->msgArray = [];
    }


}