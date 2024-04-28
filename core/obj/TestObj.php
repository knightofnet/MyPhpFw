<?php

namespace myphpfw\core\obj;

class TestObj
{
    public int $nbError;

    private String $chaineTest;

    /**
     * @return int
     */
    public function getNbError(): int
    {
        return $this->nbError;
    }

    /**
     * @param int $nbError
     */
    public function setNbError(int $nbError): void
    {
        $this->nbError = $nbError;
    }


}