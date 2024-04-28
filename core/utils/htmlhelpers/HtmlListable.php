<?php

namespace myphpfw\core\utils\htmlhelpers;

class HtmlListable
{



    /** @var string Le nom du tag HTML. */
    private string $htmlElement;

    /** @var string|null L'identifiant HTML de l'élément. */
    private ?string $idHtml;

    /** @var string[] La liste des class HTML */
    private array $listClass = [];

    /** @var bool Booléen indiquant s'il faut générer les tags entourang */
    private bool $isGenEnclosingTags = true;



    /**
     * Constructeur de la classe HtmlListable.
     *
     */
    public function __construct($id = null)
    {
        $this->setIdHtml($id);
    }

    /**
     * generateOpenHtmlTag
     * @return string
     */
    protected function generateOpenHtmlTag() : string
    {
        $pTag = "";
        if ($this->isGenEnclosingTags()) {
            $pTag .= $this->htmlElement;
            if ($this->idHtml != null) {
                $pTag .= ' id="' . $this->idHtml . '"';
            }
            if (count($this->listClass) > 0) {
                $pTag .= ' class="' . implode(" ", $this->listClass) . '"';
            }
            $pTag .= " ";
        }

        return $pTag;
    }


    /**
     * Retourne L'identifiant HTML de l'élément..
     * @return string idHtml
     */
    public function getIdHtml(): ?string
    {
        return $this->idHtml;
    }

    /**
     * Définit L'identifiant HTML de l'élément..
     * @param string|null $idHtml
     */
    public function setIdHtml(?string $idHtml)
    {
        $this->idHtml = $idHtml;
    }

    /**
     * Retourne Le nom du tag HTML..
     * @return string htmlElement
     */
    public function getHtmlElement()
    {
        return $this->htmlElement;
    }

    /**
     * Définit Le nom du tag HTML..
     * @param string|null $htmlElement
     */
    public function setHtmlElement(?string $htmlElement)
    {
        $this->htmlElement = $htmlElement;
    }


    /**
     * Retourne La liste des class HTML.
     * @return string[] listClass
     */
    public function &getListClass()
    {
        return $this->listClass;
    }

    /**
     * Retourne booléen indiquant s'il faut générer les tags entourang.
     * @return bool isGenEnclosingTags
     */
    public function isGenEnclosingTags()
    {
        return $this->isGenEnclosingTags;
    }

    /**
     * Définit booléen indiquant s'il faut générer les tags entourang.
     * @param bool $isGenEnclosingTags
     */
    public function setIsGenEnclosingTags($isGenEnclosingTags)
    {
        $this->isGenEnclosingTags = $isGenEnclosingTags;
    }
}
