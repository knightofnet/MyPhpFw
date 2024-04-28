<?php

namespace myphpfw\core\obj;

use myphpfw\core\App;
use myphpfw\core\utils\Utils;

class JsonReturnObj
{

    private string $state = "ok";

    private array $result = [];

    private array $alerts = [];

    private array $debugLines = [];

    private bool $isCommit = true;

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return JsonReturnObj
     */
    public function setState(string $state): JsonReturnObj
    {
        $this->state = $state;
        return $this;
    }

    public function addResult($name, $valMixed): JsonReturnObj
    {
        $this->result[$name] = $valMixed;

        return $this;
    }

    public function addAlert($msg, $level = "danger"): JsonReturnObj
    {
        $this->alerts[] = ["m" => $msg, "l" => $level];
        return $this;
    }

    public function addDebug(string $msg): JsonReturnObj
    {
        try {
            if (IS_DEBUG || App::$sessionObj->userConnexion()->getUserId() == 1) {
                $dateStr = new \DateTime('now');
                $key = $dateStr->format("d-m-Y-H-i-s");

                $i = 0;
                while (key_exists($key, $this->debugLines)) {
                    $key = $dateStr->format("d-m-Y-H-i-s") . "_" . $i++;
                }
                $this->debugLines[$key] = $msg;
            }

            return $this;
        } catch (\Exception $ex) {
            Utils::logWarn("JsonReturnObj::toArray", $ex);
        }

        return $this;
    }

    public function toArray(): array
    {
        $arrRet = [
            "state" => $this->state,
            "result" => $this->result,
            "alerts" => $this->alerts
        ];

        try {
            if (IS_DEBUG || App::$sessionObj->userConnexion()->getUserId() == 1) {
                $arrRet['debug'] = $this->debugLines;
            }
        } catch (\Exception $ex) {
            Utils::logWarn("JsonReturnObj::toArray", $ex);
        }

        return $arrRet;
    }

    /**
     * @return bool
     */
    public function isCommit(): bool
    {
        return $this->isCommit;
    }

    /**
     * @param bool $isCommit
     * @return JsonReturnObj
     */
    public function setIsCommit(bool $isCommit): JsonReturnObj
    {
        $this->isCommit = $isCommit;
        return $this;
    }



}