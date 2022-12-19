<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Produit\Animationproduit;
use App\Form\Produit\Produit\PartiecoursType;
use App\Service\Servicetext\GeneralServicetext;
use App\Entity\Produit\Produit\Produitpanier;

class AnimationproduitController extends AbstractController
{
public function addmentionanimation(GeneralServicetext $service, User $user, $position)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['id']))
	{
		$id = $_POST['id'];

	}else{
		$id = 0;
	}
	
	if(isset($_POST['position']))
	{
		$position = $_POST['position'];
	}else{
		$position = 0;
	}
	
	$produit = $em->getRepository(Produit::class)
	              ->find($id);
	if($produit != null)
	{
		if($position == 'aimer')
		{
			$oldanimation = $em->getRepository(Animationproduit::class)
					           ->findOneBy(array('user'=>$user,'produit'=>$produit,'aimer'=>1));
			if($oldanimation == null)
			{
				$oldanimation = new Animationproduit();
				$oldanimation->setUser($user);
				$oldanimation->setProduit($produit);
				$oldanimation->setAimer(true);
				$em->persist($oldanimation);
			
				$em->flush();
			}
			echo 1;
			exit;
		}else if($position == 'enregistrer'){
			$oldanimation = $em->getRepository(Animationproduit::class)
					           ->findOneBy(array('user'=>$user,'produit'=>$produit,'enregistrer'=>1));
			if($oldanimation == null)
			{
				$oldanimation = new Animationproduit();
				$oldanimation->setUser($user);
				$oldanimation->setProduit($produit);
				$oldanimation->setEnregistrer(true);
				$em->persist($oldanimation);
			
				$em->flush();
			}
			echo 1;
			exit;
		}else{
			echo 1;
			exit;
		}
	}else{
		echo 0;
		exit;
	}
}

public function confirmationlectrure(Chapitrecours $chapitrecours, GeneralServicetext $service, $idprodpan)
{
	$em = $this->getDoctrine()->getManager();
	
	$produitpanier = $em->getRepository(Produitpanier::class)
	                    ->find($idprodpan);
						
	if($produitpanier  != null)
	{
		$oldanimation = $em->getRepository(Animationproduit::class)
						   ->findOneBy(array('user'=>$this->getUser(),'chapitrecours'=>$chapitrecours,'produitpanier'=>$produitpanier,'voir'=>1));
		if($oldanimation == null)
		{
			$oldanimation = new Animationproduit();
			$oldanimation->setUser($this->getUser());
			$oldanimation->setChapitrecours($chapitrecours);
			$oldanimation->setProduitpanier($produitpanier);
			$oldanimation->setProduit($chapitrecours->getPartiecours()->getProduit());
			$oldanimation->setVoir(true);
			$em->persist($oldanimation);
		
			$em->flush();
		}
		echo 1;
		exit;
	}else{
		echo 0;
		exit;
	}
}
}