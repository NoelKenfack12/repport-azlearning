<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Avril 2016
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Quartier;
use App\Entity\Produit\Service\Ville;

class QuartierController extends AbstractController
{
public function autorecherchequartier()
{
	$em = $this->getDoctrine()->getManager();
	$liste_localite = $em->getRepository(Ville::class)
	                     ->findAll();
	$tab = array();
	foreach($liste_localite as $localite){
		$d = array();
		$d['drapeau'] = 'template/images/valid.png';
		$d['nom'] = $localite->getNom();
		$d['id'] = $localite->getId();
		$tab[] = $d;
	}
	return new Response(json_encode($tab));
}

public function recherchequartier()
{
	$em = $this->getDoctrine()->getManager();
	$liste_localite = $em->getRepository(Quartier::class)
	                     ->findAll();
	$tab = array();
	foreach($liste_localite as $localite){
		$d = array();
		$d['drapeau'] = 'template/images/valid.png';
		if($localite->getVille() != null)
		{
			$d['nom'] = $localite->getNom();
		}else{
			$d['nom'] = $localite->getNom();
		}
		$d['id'] = $localite->getId();
		$tab[] = $d;
	}
	return new Response(json_encode($tab));
}
}
?>