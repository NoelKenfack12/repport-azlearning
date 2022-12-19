<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2016
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Entity\Users\User\Notification;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Produitformation;
use App\Service\Servicetext\GeneralServicetext;

class SouscriptionformationController extends AbstractController
{

public function souscriptionformation(Categorie $cat)
{
	return $this->render('Theme/Produit/Produit/Souscriptionformation/souscriptionformation.html.twig',
	array('cat'=>$cat));
}

public function showformations(Categorie $cat, $ident)
{
	$em = $this->getDoctrine()->getManager();
	$liste_formation = $em->getRepository(Service::class)
	                      ->findBy(array('principal'=>1),array('rang'=>'desc'));
	if(isset($_POST['item']))
	{
		$ident = $_POST['item'];
	}else{
		$ident = $ident;
	}
	$firstoffert = $em->getRepository(Service::class)
	                      ->findOneBy(array('principal'=>1,'id'=>$ident));
	$offres = new \Doctrine\Common\Collections\ArrayCollection();
	$defaultoffert = null;
	foreach($liste_formation as $formation)
	{
		$tabscat = array();
		$i = 0;
		foreach($formation->getProduits() as $produit)
		{
			if(!in_array($produit->getSouscategorie()->getCategorie()->getId(), $tabscat))
			{
				$tabscat[$i] = $produit->getSouscategorie()->getCategorie()->getId();
				$i++;
			}
		}
		if(in_array($cat->getId(), $tabscat))
		{
			$offres[] = $formation;
			if($defaultoffert == null)
			{
				$defaultoffert = $formation;
			}else{
				if($defaultoffert->getNprix() > $formation->getNprix())
				{
					$defaultoffert = $formation;
				}
			}
		}
	}
	if($firstoffert == null)
	{
		$firstoffert = $defaultoffert;
	}
	return $this->render('Theme/Produit/Produit/Souscriptionformation/showformations.html.twig',
	array('cat'=>$cat,'em'=>$em,'offres'=>$offres,'ident'=>$ident,'firstoffert'=>$firstoffert));
}

public function asyncressourcesdwld(GeneralServicetext $service, Request $request, $type, $id, $idprodpan, $mess)
{
	$em = $this->getDoctrine()->getManager();
	if($request->getMethod() == 'POST')
	{
		if($type == 'cours')
		{
			$produit = $em->getRepository(Produit::class)
						  ->find($id);
			if($produit != null)
			{
				$nextchapter = $em->getRepository(Chapitrecours::class)
						          ->firstChapitreCours($produit->getId());
						  
				return $this->render('Theme/Produit/Produit/Souscriptionformation/asyncressourcesdwld.html.twig',
				array('produit'=>$produit,'type'=>$type,'nextchapter'=>$nextchapter,'idprodpan'=>$idprodpan,'mess'=>$mess));
			}else{
				echo 0;
				exit;
			}
		}else if($type == 'formation')
		{
			$service = $em->getRepository(Service::class)
						  ->find($id);
			if($service != null)
			{
				$nextcourse = $em->getRepository(Produitformation::class)
						         ->findOneBy(array('service'=>$service), array('rang'=>'asc'),1);
				return $this->render('Theme/Produit/Produit/Souscriptionformation/asyncressourcesdwld.html.twig',
				array('service'=>$service,'type'=>$type,'nextcourse'=>$nextcourse,'idprodpan'=>$idprodpan,'mess'=>$mess));
			}else{
				echo 0;
				exit;
			}
		}else if($type == 'chapitre')
		{
			$chapitre = $em->getRepository(Chapitrecours::class)
						   ->find($id);
			if($chapitre != null)
			{
				$liste_chapitre = $em->getRepository(Chapitrecours::class)
						             ->listechapitrecours($chapitre->getPartiecours()->getProduit()->getId());
				
				$loopchapter = false;
				$nextchapter = null;
				foreach($liste_chapitre as $cureentchapter)
				{
					if($cureentchapter == $chapitre)
					{
						$loopchapter = true;
					}else if($loopchapter == true)
					{
						$nextchapter = $cureentchapter;
						break;
					}
				}
				return $this->render('Theme/Produit/Produit/Souscriptionformation/asyncressourcesdwld.html.twig',
				array('chapitre'=>$chapitre,'type'=>$type,'nextchapter'=>$nextchapter,'idprodpan'=>$idprodpan,'mess'=>$mess));
			}else{
				echo 0;
				exit;
			}
		}else{
			echo 0;
		    exit;
		}
	}else{
		echo 0;
		exit;
	}
}

}