<?php

namespace myphpfw\core\utils\template_engine;

use myphpfw\core\utils\lang\StringUtils;

class TemplateCallFunc
{

    private array $method;
    private array $args;

    public function __construct(array $method, array $args = [])
    {
        $this->method = $method;
        $this->args = $args;
    }

    /**
     * @param TemplateVar[] $templateOtherVars
     * @return void
     */
    public function getResultFn(array $templateOtherVars)
    {

        foreach ($this->args as &$arg) {
            $vArg = $arg;
            $vArg = StringUtils::str_replace_first("%", "{{", $vArg, );
            $vArg = StringUtils::str_replace_first("%", "}}", $vArg, );
            foreach ($templateOtherVars as $var) {
                //$var->addValue($varElt);
                $arrRpl = $var->getReplacementsArray($templateOtherVars);
                $vArg = str_replace($arrRpl['s'], $arrRpl['r'], $vArg);

            }

            $arg = $vArg;
        }

    }

}