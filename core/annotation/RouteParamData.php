<?php

namespace myphpfw\core\annotation;

/**
 * @Annotation
 */
class RouteParamData
{
    private string $paramName;
    private ?string $paramType = null;
    /**
     * @var mixed
     */
    private ?bool $canBeNullOrEmpty = null;
    /**
     * @var mixed
     */
    private ?string $regexValid = null;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $v = $values['value'];
            if (is_array($v)) {
               throw new \Exception('RouteParamData: value must be a string or associative array with key "name"');
            } else {
                $this->paramName = $v;
            }
        }

        if (isset($values['name'])) {
            $this->paramName = $values['name'];
        }
        if (isset($values['type'])) {
            $this->paramType = $values['type'];
        }
        if (isset($values['canBeNullOrEmpty'])) {
            $this->canBeNullOrEmpty = $values['canBeNullOrEmpty'];
        }
        if (isset($values['regexValid'])) {
            $this->regexValid = $values['regexValid'];
        }



    }

    /**
     * @return string
     */
    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * @return string|null
     */
    public function getParamType(): ?string
    {
        return $this->paramType;
    }

    /**
     * @return bool|null
     */
    public function getCanBeNullOrEmpty(): ?bool
    {
        return $this->canBeNullOrEmpty;
    }

    /**
     * @return string|null
     */
    public function getRegexValid(): ?string
    {
        return $this->regexValid;
    }


}