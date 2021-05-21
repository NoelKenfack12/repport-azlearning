<?php

namespace App\Entity\General\Template;

use App\General\Validator\Validatortext\Taillemin;

class Recherche
{
   /**
     *@Taillemin(valeur=3, message="Entrer au moins 3 caract�s")
     */
    private $donnee;
	public function getDonnee()
	{
	return $this->donnee;
	}
	public function setDonnee($donnee)
	{
	$this->donnee = $donnee;
	}
}