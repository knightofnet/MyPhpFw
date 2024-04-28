<?php

namespace php\core\utils\htmlhelpers;

class GenericHtmlTable extends HtmlTable
{

    public function __construct($id = null)
    {
        parent::__construct($id);
    }

    public function addColumn(String $headerName, \Closure $readAction) : GenericHtmlTable {
        $col = new HtmlColumn();
        $col->setHeader($headerName);
        $col->setCallback($readAction);
        $this->getColumns()[] = $col;

        return $this;
    }

    public function initColumns()
    {
        // TODO: Implement initColumns() method.
    }
}