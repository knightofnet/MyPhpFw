<?php

namespace myphpfw\core\obj;

use Closure as ClosureAlias;

class ReflectionToCallMethod
{

    private string $propName;

    private string $type;

    private string $innerType;

    private ?ClosureAlias $closure;

    private ?\ReflectionMethod $reflectionMethod;

    /**
     * @return string
     */
    public function getPropName(): string
    {
        return $this->propName;
    }

    /**
     * @param string $propName
     * @return ReflectionToCallMethod
     */
    public function setPropName(string $propName): ReflectionToCallMethod
    {
        $this->propName = $propName;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ReflectionToCallMethod
     */
    public function setType(string $type): ReflectionToCallMethod
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return ClosureAlias|null
     */
    public function getClosure(): ?ClosureAlias
    {
        return $this->closure;
    }

    /**
     * @param ClosureAlias|null $closure
     * @return ReflectionToCallMethod
     */
    public function setClosure(?ClosureAlias $closure): ReflectionToCallMethod
    {
        $this->closure = $closure;
        return $this;
    }

    /**
     * @return \ReflectionMethod|null
     */
    public function getReflectionMethod(): ?\ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    /**
     * @param \ReflectionMethod|null $reflectionMethod
     * @return ReflectionToCallMethod
     */
    public function setReflectionMethod(?\ReflectionMethod $reflectionMethod): ReflectionToCallMethod
    {
        $this->reflectionMethod = $reflectionMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getInnerType(): string
    {
        return $this->innerType;
    }

    /**
     * @param string $innerType
     * @return ReflectionToCallMethod
     */
    public function setInnerType(string $innerType): ReflectionToCallMethod
    {
        $this->innerType = $innerType;
        return $this;
    }








}