<?php

namespace php\core\utils\lang\collection;

class ArrayStack
{

    private array $_innerArray = [];

    /**
     * @param mixed ...$values
     * @return void
     */
    public function push($value)
    {
        $c = count($this->_innerArray);
        $this->_innerArray[$c] = $value;

    }

    /**
     * @return mixed|null
     */
    public function &peek()
    {
        $c = count($this->_innerArray);
        if ($c == 0) {
            throw new \UnderflowException("Cannot peek on empty Stack");
        }

        $maxIx = $c - 1;
        return $this->_innerArray[$maxIx];
    }

    /**
     * @return mixed|null
     */
    public function pop()
    {
        $c = count($this->_innerArray);
        if ($c == 0) {
            return null;
        }

        $maxIx = $c - 1;
        $val = $this->_innerArray[$maxIx];
        unset($this->_innerArray[$maxIx]);
        return $val;
    }

    public function isEmpty(): bool
    {
        return count($this->_innerArray) == 0;
    }

    public function count(): int
    {
        return count($this->_innerArray);
    }

    public function clear(): void
    {
        $this->_innerArray = [];
    }

}