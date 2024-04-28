<?php

namespace myphpfw\core\obj;

use Closure as ClosureAlias;
use myphpfw\core\App;
use myphpfw\core\utils\lang\ArrayUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\TemplateEngine;
use myphpfw\core\utils\TemplateEngineEnhanced;
use myphpfw\core\utils\Utils;

class ResponseHttp
{

    private int $codeHttp = 200;

    /** @var ClosureAlias|null */
    private ?ClosureAlias $action = null;

    /** @var string[] */
    private array $headers = [];

    private bool $isSaveToHistory = false;
    private ?string $titlePage = null;


    public static function ResultsObjectToJson($object, int $codeHttp = 200): ResponseHttp
    {

        $rep = new ResponseHttp();
        $rep->codeHttp = $codeHttp;
        $rep->headers[] = 'Content-Type: application/json; charset=utf-8';
        $rep->action = function () use ($object) {
            echo json_encode(StringUtils::utf8ize($object), JSON_UNESCAPED_UNICODE);
        };

        return $rep;


    }

    public static function ResultCodeHttp(int $codeHttp = 200): ResponseHttp
    {
        $rep = new ResponseHttp();
        $rep->codeHttp = $codeHttp;
        return $rep;
    }

    public static function RedirectTo(string $routeToRedirect, int $codeHttp = 200): ResponseHttp
    {
        $rep = new ResponseHttp();
        $rep->codeHttp = $codeHttp;
        $rep->headers[] = "Location: " . $routeToRedirect;
        return $rep;
    }

    public static function DownloadFile(string $fileUrl, string $fileName, string $contentType = "application/octet-stream"): ResponseHttp
    {
        $rep = new ResponseHttp();
        $rep->codeHttp = 200;
        $rep->headers[] = 'Content-Type: ' . $contentType;
        $rep->headers[] = 'Content-Transfer-Encoding: Binary';
        $rep->headers[] = 'Content-disposition: attachment; filename="' .  $fileName . '"';

        $rep->action = function () use ($fileUrl) {
            if (readfile($fileUrl) !== false) {
                unlink($fileUrl);
            }
        };
        return $rep;
    }
    public static function ResultsBlade($blade, string $tpl, array $tplVars, int $codeHttp = 200, bool $saveToHistory = false): ResponseHttp
    {
        //$tplVars["tplContent"] = $tpl;
        $mainTpl = "main_tpl";


        $rep = new ResponseHttp();
        $rep->isSaveToHistory = $saveToHistory;
        if ($rep->isSaveToHistory) {
            $rep->titlePage = ArrayUtils::tryGet('title', $tplVars, null);
        }
        $rep->codeHttp = $codeHttp;
        $rep->action = function () use ($blade, $mainTpl, $tpl, $tplVars) {
            echo $blade->run($tpl, $tplVars);
        };

        return $rep;

    }

    public function doResponse()
    {
        http_response_code($this->codeHttp);

        foreach ($this->headers as $header) {
            header($header);
        }

        if (null != $this->action) {
            call_user_func($this->action);
        }
    }

    /**
     * @return bool
     */
    public function isSaveToHistory(): bool
    {
        return $this->isSaveToHistory;
    }

    /**
     * @return string|null
     */
    public function getTitlePage(): ?string
    {
        return $this->titlePage;
    }


}