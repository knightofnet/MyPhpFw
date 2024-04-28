<?php

namespace myphpfw\core\utils\htmlhelpers;

abstract class  HtmlTable extends HtmlListable
{


    /** @var string Le code HTMl généré */
    private string $htmlContent;

    /** @var HtmlColumn[] Les colonnes */
    protected array $columns = [];

    /**
     * Constructeur de la classe HtmlTable.
     *
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->setHtmlElement("table");
    }



    /**
     * Retourne les colonnes.
     * @return HtmlColumn[] columns
     */
    public function &getColumns() : array
    {
        return $this->columns;
    }



    /**
     * Retourne Le code HTMl généré.
     * @return string htmlContent
     */
    public function getHtmlContent() : string
    {
        return $this->htmlContent;
    }


    /**
     * generateOpenHtmlTag
     * @return string
     */
    protected function generateOpenHtmlTag() : string
    {
        $pTag = parent::generateOpenHtmlTag();
        //$pTag .= " ";

        return $pTag;
    }




    /**
     * @return string
     */
    public function generate(array $listObj) : string
    {
        $r = "";
        if ($this->isGenEnclosingTags()) {
            $r .= '<' . $this->generateOpenHtmlTag() . '>';
        }

        $r .= '<thead><tr>';
        /* Headers */
        foreach ($this->columns as $column) {
            $r .= '<th>' . $column->getHeader() . '</th>';
        }

        $r .= '</tr></thead>';


        /* Lignes */
        $r .= '<tbody>';
        foreach ($listObj as $obj) {
            $r .= '<tr>';
            foreach ($this->columns as $column) {
                $retCall = $column->getCallback()($obj);
                if ($retCall instanceof \DateTime) {
                    $retCall = $retCall->format("c");
                }
                $r .= '<td>' . $retCall . '</td>';
            }
            $r .= '</tr>';
        }
        $r .= '</tbody>';

        if ($this->isGenEnclosingTags()) {
            $r .= '</' . $this->getHtmlElement() . '>';
        }

        return $r;
    }

    public abstract function initColumns();
}
