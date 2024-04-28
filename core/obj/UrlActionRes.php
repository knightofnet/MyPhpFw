<?php

namespace myphpfw\core\obj;

class UrlActionRes
{
    private bool $isValidAction = false;

    private string $routeName;

    private \Closure $routeAction;

    private array $varsUrl = [];

    /**
     * @return bool
     */
    public function isValidAction(): bool
    {
        return $this->isValidAction;
    }

    /**
     * @param bool $isValidAction
     * @return UrlActionRes
     */
    public function setIsValidAction(bool $isValidAction): UrlActionRes
    {
        $this->isValidAction = $isValidAction;
        return $this;
    }

    /**
     * @return array
     */
    public function &getVarsUrlByRef(): array
    {
        return $this->varsUrl;
    }

    /**
     * @return array
     */
    public function &getVarsUrl(): array
    {
        return $this->varsUrl;
    }

    /**
     * @param array $varsUrl
     * @return UrlActionRes
     */
    public function setVarsUrl(array $varsUrl): UrlActionRes
    {
        $this->varsUrl = $varsUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     * @return UrlActionRes
     */
    public function setRouteName(string $routeName): UrlActionRes
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * @return \Closure
     */
    public function getRouteAction(): \Closure
    {
        return $this->routeAction;
    }

    /**
     * @param \Closure $routeAction
     * @return UrlActionRes
     */
    public function setRouteAction(\Closure $routeAction): UrlActionRes
    {
        $this->routeAction = $routeAction;
        return $this;
    }



    private function hasVars() : bool {
        return count($this->varsUrl) > 0;
    }
}