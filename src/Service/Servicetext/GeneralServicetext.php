<?php
//(c) Noel Kenfack   Novembre 2016
namespace App\Service\Servicetext;

class GeneralServicetext
{
private $bareme;

public function __construct($bareme=100)
{
	$this->bareme = $bareme;
}
public function normaliseText($text)
{
 $text = trim($text); //retire les caractères vide en début et en fin du text.
 $text = $this->retireAccent($text);
 $text =  strtolower($text);
 $text = str_replace("'", "-", $text);
 $text = str_replace(" ", "-", $text); 
 $text = str_replace("_", "-", $text);
 return $text; 
}

public function chaineValide($text,$valmin,$valmax)
{
	$text = trim($text);
	$tail = strlen($text);
	if ($valmin <= $tail and $valmax >= $tail)
	{
		return true;
	}else{
		return false; 
	}
}

public function codepays($text)
{
	$regex = '#^[+-]([0-9]){1,10}$#';
	if (preg_match($regex, $text) || $text == null)
	{
		return true;
	}else{
		return false; 
	}
}

// cette fonction recherche les éléments de tab1 dans la variable texte et remplace par les éléments de tab2 de la même position.
public function retireAccent($text)
{
	$tab1 = array('é','è','à','ù','ç','_','ô','ê','î','ï','ö','ë');
	$tab2 = array('e','e','a','u','c','-','o','e','i','i','o','e');
	$text = str_ireplace($tab1, $tab2, $text);
	return $text;
}

public function email($text)
{
	$regex ='#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}#i';
	if(preg_match($regex, $text) || $text == null)
	{
		return true;
	}else{
		return false; 
	}
}

public function siteweb($text)
{
	$regex ='#(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?#i';
	if (preg_match($regex, $text) || $text == null)
	{
		return true;
	}else{
		return false; 
	}
}

public function tel($text)
{
	$regex ='#^[0-9][0-9]{6,20}$#';
	if (preg_match($regex, $text))
	{
		return true;
	}else{
		return false; 
	}	
}

public function password($text)
{
	$regex = '#^[a-z0-9]([a-z0-9][\$\(-_\.]?){6,150}$#i';
	if (preg_match($regex, $text))
	{
		return true;
	}else{
		return false; 
	}
}

public function telephone($text)
{
	$regex = '#^[0-9][0-9]{6,10}$#';
	if (preg_match($regex, $text) || $text == null)
	{
		return true;
	}else{
		return false; 
	}
}

/**
cette fonction prend une liste d'élément et choisi d'une manière aléatoire nbre_max d'élement d'ans la liste
*/
public function selectEntities($liste_entity, $nbre_max)
{
	$nb_entity = count($liste_entity);
	if ($nb_entity <= $nbre_max)
	{
	 $tail = $nb_entity;
	}else{
	 $tail = $nbre_max;
	}
	if($nb_entity > 0){
	$tab = range(0,$nb_entity - 1);
	$cle = array_rand($tab,$tail);
	}
	$etab_aleatoires = new \Doctrine\Common\Collections\ArrayCollection();
	$collection = 0;
	$compt = 0;
	foreach($liste_entity as $entity)
	{
	   if (is_int($cle) || in_array($compt, $cle))
	   {
	   $etab_aleatoires[] = $entity;
	   $collection++;
	   }
	   $compt++;
	   if($collection == $tail)
	   {
	   break;
	   }
	}
	return $etab_aleatoires;
}

/**
	cette fonction prend une liste d'élément et choisi d'une manière aléatoire 1 élement d'ans la liste
*/
public function selectEntity($entites)
{
    $nbreetab = count($entites);
	if ($nbreetab == 0){
		$nbreetab = 1;
		$etabaleatoire = null;
	}
	$numero = mt_rand(0, ($nbreetab - 1));
	$compteur = 0;
	foreach($entites as $entite)
	{
		if ( $compteur == $numero )
		{ 
			$etabaleatoire = $entite;
			break;
		}
		$compteur = $compteur + 1;
	}
	return $etabaleatoire;
}

public function mydate($text)
{
	$regex = '#^[0-3]{0,1}[0-9]/[0-1]{0,1}[0-9]/[0-9]{2,4}$#';
	if (preg_match($regex, $text))
	{
	$valid = true;
	$tab = explode('/',$text);
		if($tab[0] > 31 || $tab[1] > 12 || $tab[2] > 5000)
		{
			$valid = false;
		}else{
			if($tab[0] > 29 and $tab[1] == 02 )
			{
				$valid = false;
			}
			$valeur = array(4, 6, 9, 11);
			if($tab[0] > 30 and  in_array($tab[1],$valeur))
			{
				$valid = false;
			}
		}
		return $valid;
	}else{
		return false; 
	}
}

public function normaliseDate($text)
{
	$text = trim($text); //retire les caractères vide en début et en fin du text.
	$text =  strtolower($text);
	$tab = explode('/',$text);
	$tail = count($tab);
	$tableau = null;
	for ($i = 0; $i < $tail; $i++)
	{
	$tableau[$i] = $tab[$tail - 1 - $i];
	}
	$text = implode('/',$tableau);
	$text = str_replace("/", "-", $text);
	return $text; 
}

public function getBareme()
{
	return $this->bareme;
}

public function decrypt($message, $key)
{
  $decrypted = Cryptor::Decrypt($message, $key);
  
  $decrypted = substr($decrypted, 0, -16); //Nous coupons les caractères injectés précedemment.
  return $decrypted;
}

public function encrypt($message, $key)
{
  $encrypted = Cryptor::Encrypt($message.'AbcDefGhiJKlmNoP', $key);//Nous avons injecté ces caractères pour faire en sorte que toute mot à cripter ai plus de 16 caractère.
  return $encrypted;
}

}