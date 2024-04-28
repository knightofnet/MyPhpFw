<?php

namespace myphpfw\core\obj;

class CurlConfig
{
    private int $port = 80;
    private int $connectTimeout = 1;

    /**
     * @var string GET, POST, PUT, DELETE
     */
    private string $method = "GET";

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return CurlConfig
     */
    public function setPort(int $port): CurlConfig
    {
        $this->port = $port;
        return $this;
    }



    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     * @return CurlConfig
     */
    public function setConnectTimeout(int $connectTimeout): CurlConfig
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
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
     * @return CurlConfig
     */
    public function setMethod(string $method): CurlConfig
    {
        $this->method = $method;
        return $this;
    }





}