<?php

namespace php\core\utils\template_engine;

class TemplateContainer
{

    /**
     * @var TemplateContainer[]
     */
    private array $containers = [];

    private string $containerName = "";

    private int $localIxCont = 0;

    private ?TemplateContainer $currentContainer = null;

    /** @var string[]  */
    private array $lines = [];

    private string $containerVarName = "";
    private string $containerType = "";

    /**
     * @var string[]
     */
    private array $otherData = [];

    private ?TemplateContainer $parentCoontainer = null;

    public function __construct(string $name)
    {
        $this->containerName = $name;
    }

    public function addSubContainer(?string $containerName = null): TemplateContainer
    {
        $nCName = $this->containerName . '_';
        if ($containerName != null) {
            $nCName .= $containerName . "_";
        }
        $nCName .= $this->localIxCont++;

        $nC = new TemplateContainer($nCName);
        $this->containers[$nCName] = $nC;

        $nC->parentCoontainer = $this;


        $this->currentContainer = $nC;

        return $nC;

    }

    /**
     * @return TemplateContainer|null
     */
    public function getCurrentSubContainer() : ?TemplateContainer
    {
        return $this->currentContainer;
    }

    public function addLine($line)
    {
        $this->lines[] = $line;

    }

    public function addOtherData($key, $value) {
        $this->otherData[$key] = $value;
    }

    public function getName(): string
    {
        return $this->containerName;
    }

    /**
     * @return TemplateContainer[]
     */
    public function getSubContainers(): array
    {
        return $this->containers;
    }

    /**
     * @return string[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @return string
     */
    public function getContainerVarName(): string
    {
        return $this->containerVarName;
    }

    /**
     * @param string $containerVarName
     * @return TemplateContainer
     */
    public function setContainerVarName(string $containerVarName): TemplateContainer
    {
        $this->containerVarName = $containerVarName;
        return $this;
    }

    /**
     * @return string
     */
    public function getContainerType(): string
    {
        return $this->containerType;
    }

    /**
     * @param string $containerType
     * @return TemplateContainer
     */
    public function setContainerType(string $containerType): TemplateContainer
    {
        $this->containerType = $containerType;
        return $this;
    }

    /**
     * @return TemplateContainer|null
     */
    public function getParentCoontainer(): ?TemplateContainer
    {
        return $this->parentCoontainer;
    }

    /**
     * @param TemplateContainer|null $parentCoontainer
     * @return TemplateContainer
     */
    public function setParentCoontainer(?TemplateContainer $parentCoontainer): TemplateContainer
    {
        $this->parentCoontainer = $parentCoontainer;
        return $this;
    }

    /**
     * @return array
     */
    public function getOtherData(): array
    {
        return $this->otherData;
    }




    public function __toString()
    {
        return $this->getName() . "[containersNbr:" .count($this->containers) . ", linesNbr:".count($this->lines)."]";
    }

}