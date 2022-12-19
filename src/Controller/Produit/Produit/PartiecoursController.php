<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\UserBundle\Entity\User;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Produitpanier;
use App\Form\Produit\Produit\PartiecoursType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Pricing\Offre\Abonnementuser;

class PartiecoursController extends AbstractController
{
public function addpartiecours(Produit $produit, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$partie = new Partiecours();
	$form = $this->createForm(PartiecoursType::class, $partie);

	if ($request->getMethod() == 'POST'){
    $form->handleRequest($request);
	if($form->isValid()){
		$partie->setProduit($produit);
		$produit->setDate(new \Datetime());
		$produit->setTimestamp(time());
		$em->persist($partie);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Votre cours a été mise à jour avec succès.');

	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!! Vérifier la taille du titre [3,70]');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function detailpartie(Partiecours $partie, $number, $addform, $codeadmin = 0, $idpartie = 0, $idchapitre = 0, $idprodpan = 0, $firtPart = false)
{
	$em = $this->getDoctrine()->getManager();
	$formedit = $this->createForm(PartiecoursType::class, $partie); 
	$partie->setEm($em);

	$produitpanier = $em->getRepository(Produitpanier::class)
	                        ->find($idprodpan);

	$repository = $em->getRepository(Abonnementuser::class);
    $abonnementuser = $repository->findOneBy(array('user'=>$this->getUser(), 'active'=>1));

	return $this->render('Theme/Produit/Produit/Partiecours/detailpartie.html.twig', 
	array('partie'=>$partie,'produit'=>$partie->getProduit(),'number'=>$number,'codeadmin'=>$codeadmin,'formedit'=>$formedit->createView(),
	'addform'=>$addform, 'idpartie'=>$idpartie, 'idchapitre'=>$idchapitre, 'produitpanier'=>$produitpanier, 'firtPart'=>$firtPart, 'abonnementuser'=>$abonnementuser));
}

public function modificationpartiecours(Partiecours $partie, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();

	$formedit = $this->createForm(PartiecoursType::class, $partie); 
	$produit = $partie->getProduit();
	if ($request->getMethod() == 'POST'){
    $formedit->handleRequest($request);
	if($formedit->isValid()){
		$produit->setDate(new \Datetime());
		$produit->setTimestamp(time());
		$em->persist($partie);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Votre cours a été mise à jour avec succès.');

	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!! Vérifier la taille du titre [3,70]');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function supprimerpartiecours(Partiecours $partie)
{
	$em = $this->getDoctrine()->getManager();
	$produit = $partie->getProduit();
	$liste_chapitre = $em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$partie), array('rang'=>'asc'));
	if($produit->getUser() == $this->getUser())
	{
		if(count($liste_chapitre) == 0)
		{
			$em->remove($partie);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Votre cours a été mise à jour avec succès.');
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec ! Supprimer d\'abord les chapitres liés à cette partie.');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec !! Vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}
}