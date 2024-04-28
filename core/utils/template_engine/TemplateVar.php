<?php

namespace myphpfw\core\utils\template_engine;

use myphpfw\core\utils\lang\ArrayUtils;
use myphpfw\core\utils\lang\ReflectionUtils;
use myphpfw\core\utils\lang\StringUtils;
use myphpfw\core\utils\Utils;

class TemplateVar
{

    private string $varName;

    /**
     * @var string[]
     */
    private array $varSubPath = [];

    private string $varNameFull;

    /** @var string[]  */
    private array $varCapture = [];

    private $value;

    private ?TemplateCallFunc $callFunc = null;

    public function __construct(string $varNameFull)
    {

        $this->varNameFull = $varNameFull;
        if (StringUtils::indexOf($this->varNameFull, ".") > 0 ) {
            $exploded = explode(".", $this->varNameFull);
            $this->varName = $exploded[0];
            unset($exploded[0]);
            $this->varSubPath = $exploded;
        }  else {
            $this->varName = $varNameFull;
        }


    }

    private static function getArrayWalk($value, array $array)
    {
        $retValue = null;
        if (empty($array)) {
            return $value;
        }
        $key = ArrayUtils::first($array);
        $nArray = ArrayUtils::skip($array, 1);
        if (is_array($value) && key_exists($key, $value)) {
            return self::getArrayWalk($value[$key], $nArray);
        } else if (is_object($value) && ReflectionUtils::isPropertyExists($key, $value)) {
            $valueVal = ReflectionUtils::getPropertyValue($key, $value);
            return self::getArrayWalk($valueVal, $nArray);
        }

        Utils::logDebug("getArrayWalk() => $key n'existe en tant que sous-clef du tableau suivant", $array);
        return null;


    }

    /**
     * @return array
     */
    public function getVarCapture(): array
    {
        return $this->varCapture;
    }


    public function addCapture(...$captures) : TemplateVar {
        foreach ($captures as  $capture) {
            if (!in_array($capture, $this->varCapture)) {
                $this->varCapture[] = $capture;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getVarName(): string
    {
        return $this->varName;
    }

    /**
     * @return string
     */
    public function getVarSubPath(): string
    {
        return $this->varSubPath;
    }

    /**
     * @return string
     */
    public function getVarNameFull(): string
    {
        return $this->varNameFull;
    }

    public function addValue($contenu)
    {
        if (is_array($contenu) && count($contenu) == 0) {
            $contenu = "";
        }
        $this->value = $contenu;
    }

    /**
     *
     * @param TemplateVar[] $templateOtherVars
     * @return array<array<String, String>>
     */
    public function getReplacementsArray(array $templateOtherVars) : array
    {
        $retArray['s'] = [];
        $retArray['r'] = [];

        foreach ($this->varCapture as $vCap) {
            $retArray['s'][] = $vCap;
            $valueReplacement = "";
            if ($this->callFunc != null) {
                $c = $this->callFunc;
                $valueReplacement = $c->getResultFn(ArrayUtils::where(fn(TemplateVar $v) => $v->getVarName() != $this->getVarName() , $templateOtherVars));

            } elseif (count($this->varSubPath) > 0) {
                $vFinale = self::getArrayWalk($this->value, $this->varSubPath);
                if (is_array($vFinale)) {
                    $vFinale = "[" . implode(", ", $vFinale) . "]";
                } else {
                    $vFinale = strval($vFinale);
                }
                $valueReplacement = $vFinale;
            } else {
                $valueReplacement = $this->value;
            }
            if (is_array($valueReplacement)) {
                $valueReplacement = "";
            }
            $retArray['r'][] = $valueReplacement;
        }

        return $retArray;
    }

    public function getValue()
    {
        return self::getArrayWalk($this->value, $this->varSubPath);
    }

    /**
     * @return TemplateCallFunc|null
     */
    public function getCallFunc(): ?TemplateCallFunc
    {
        return $this->callFunc;
    }

    /**
     * @param TemplateCallFunc|null $callFunc
     * @return TemplateVar
     */
    public function setCallFunc(?TemplateCallFunc $callFunc): TemplateVar
    {
        $this->callFunc = $callFunc;
        return $this;
    }


}