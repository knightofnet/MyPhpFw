<?php

namespace myphpfw\core\annotation;

/**
 * @Annotation
 */
class ApiRoute
{
    private string $method ;
    private string $routePath ;

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            throw new \Exception('ApiRoute: value must be a string or associative array with key "method" and "route"');
        }

        if (isset($values['method'])) {
            $this->method = strtoupper($values['method']);
        }
        if (isset($values['route'])) {
            $this->routePath = $values['route'];
        }


    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return ApiRoute
     */
    public function setMethod(string $method): ApiRoute
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoutePath(): string
    {
        return $this->routePath;
    }

    /**
     * @param string $routePath
     * @return ApiRoute
     */
    public function setRoutePath(string $routePath): ApiRoute
    {
        $this->routePath = $routePath;
        return $this;
    }




}