<?php

namespace myphpfw\core\exception;

class RerouteException extends \Exception
{

    private string $routeName;

    public function __construct(string $routeName, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->routeName = $routeName;
    }

    /**
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }





}