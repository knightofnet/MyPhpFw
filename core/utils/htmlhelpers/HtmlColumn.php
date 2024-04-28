<?php

namespace php\core\utils\htmlhelpers;

use Closure as ClosureAlias;

class HtmlColumn {

    /** @var string L'entete de la colonnne */
    private string $header;

    /** @var ClosureAlias Le code a exécuter lors de l'appel pour générer la cellule */
    private ClosureAlias $callback;

    /**
     * Retourne le code a exécuter lors de l'appel pour générer la cellule.
     * @return ClosureAlias callback
     */
    public function getCallback() : ClosureAlias {return $this->callback;}

    /**
     * Définit le code a exécuter lors de l'appel pour générer la cellule.
     * @param ClosureAlias $callback
     */
    public function setCallback(ClosureAlias $callback) { $this->callback = $callback;}

    /**
     * Retourne l'entete de la colonnne.
     * @return string header
     */
    public function getHeader() : string {return $this->header;}

    /**
     * Définit l'entete de la colonnne.
     * @param string $header
     */
    public function setHeader(string $header) { $this->header = $header;}

}