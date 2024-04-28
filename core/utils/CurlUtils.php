<?php

namespace myphpfw\core\utils;

use php\app\entities\ResourceDto;
use php\app\modules\common\RessourcesFromServer;
use myphpfw\core\obj\CurlConfig;

class CurlUtils
{
    public static function pingUrl($url, ?CurlConfig $curlConfig = null) : \stdClass
    {
        if ($curlConfig == null) {
            $curlConfig = new CurlConfig();
        }

        $innerConf = new \stdClass();
        $innerConf->curlOptReturnTransfer = false;

        $ret = self::genCurlCall($url, $curlConfig, $innerConf);

        return $ret ;
    }



    public static function getContentFromUrl($url, ?CurlConfig $curlConfig = null) : \stdClass {

        if ($curlConfig == null) {
            $curlConfig = new CurlConfig();
        }

        $innerConf = new \stdClass();
        $innerConf->curlOptReturnTransfer = true;

        $ret = self::genCurlCall($url, $curlConfig, $innerConf);

        //$retVar = new RessourcesFromServer();

        if (empty($ret->errMsg)) {
            $ret->isUp = true;
            $rSc = json_decode($ret->result);
            foreach ($rSc as $rs) {
                $ret->resources[] = $rs;
            }


        } else {
            $ret->isUp = false;
        }
        //var_dump($ret);

        return $ret;
    }

    private static function genCurlCall($url, CurlConfig $curlConfig, \stdClass $innerConf) : \stdClass
    {
        //  Initiate curl
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $innerConf->curlOptReturnTransfer);
        ///$this->cookieFile = "./cj/ck-" . Utils::generateRandomString(32) . "txt";
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $curlConfig->getPort());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $curlConfig->getConnectTimeout());
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        //curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        self::addMethodToCurl($curlConfig, $ch);
        // Execute
        $result = curl_exec($ch);
        $err = curl_error($ch);
        // Closing
        curl_close($ch);

        $ret = new \stdClass();
        $ret->result = $result;
        $ret->errMsg = $err;


        return $ret;
    }

    /**
     * @param CurlConfig|null $curlConfig
     * @param bool $ch
     * @return void
     */
    private static function addMethodToCurl(CurlConfig $curlConfig, $ch): void
    {
        if ($curlConfig->getMethod() != "GET") {
            if ("POST" == $curlConfig->getMethod()) {
                // TODO POst
                curl_setopt($ch, CURLOPT_POST, 1);
            } else if ("PUT" == $curlConfig->getMethod()) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            } else if ("DELETE" == $curlConfig->getMethod()) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            }

        }
    }

}