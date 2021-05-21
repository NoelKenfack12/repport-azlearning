<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*ce fichier est une proprietéde Zentsoft, 16 février 2015 (01h04min)--debut du Module utilisateurs
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Cataloguechantier;
use App\Entity\Produit\Produit\Recommandation;
use App\Entity\Produit\Produit\Animationproduit;
use App\Form\Produit\Produit\CataloguechantierType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;

class CataloguechantierController extends AbstractController
{
public function modifcatalogue(GeneralServicetext $service, Request $request, $id)
{
	$em = $this->getDoctrine()->getManager();
	
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = $id;
	}
	
	$categorie = $em->getRepository(Cataloguechantier::class)
					->find($id);

	if($categorie != null)
	{
		$form = $this->createForm(CataloguechantierType::class, $categorie);
		if ($request->getMethod() == 'POST'){
			$form->handleRequest($request);
			$categorie->setServicetext($service);
			if($form->isValid()){
				$em->flush();
				$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
			}else{
				$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
			}
			return $this->redirect($this->generateUrl('users_adminuser_catalogue_chantier_pro'));
		}
		return $this->render('Theme/Users/Adminuser/Cataloguechantier/modifcatalogue.html.twig',
		array('form'=>$form->createView(),'categorie'=>$categorie));
	}else{
		echo 'Echec ! Une erreur a été rencontrée.';
		exit;
	}
}

public function supprimercatalogue(Cataloguechantier $catalogue, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 

	if ($request->getMethod() == 'POST'){
	$formsupp->handleRequest($request);
    if ($formsupp->isValid()){
		
		$em->remove($catalogue);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Catégorie bien supprimée.');
			
		return $this->redirect($this->generateUrl('users_adminuser_catalogue_chantier_pro'));
	}
	}
    $this->get('session')->getFlashBag()->add('supprime_continent',$catalogue->getId());
	$this->get('session')->getFlashBag()->add('supprime_continent',$catalogue->getNom());
	return $this->redirect($this->generateUrl('users_adminuser_catalogue_chantier_pro'));
}

public function cataloguechantier(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $categorie = new Cataloguechantier($service);
	$form = $this->createForm(CataloguechantierType::class, $categorie);

	if($request->getMethod() == 'POST'){
		$form->handleRequest($request);
		if ($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$categorie->setUser($this->getUser());
			$em->persist($categorie);
			$em->flush();
		$this->get('session')->getFlashBag()->add('information','la catégorie a été bien enregistrée.');
		}
	}
	
	$liste_categorie = $em->getRepository(Cataloguechantier::class)
	                 ->findAll();

    $formsupp = $this->createFormBuilder()->getForm(); 					 
	return $this->render('Theme/Users/Adminuser/Cataloguechantier/cataloguechantier.html.twig', 
	array('form' => $form->createView(),'liste_categorie'=>$liste_categorie,'formsupp'=>$formsupp->createView()));
}

public function updaterocommandation($id, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = $id;
	}
	
	$categorie = $em->getRepository(Cataloguechantier::class)
					->find($id);

	if($categorie != null)
	{
		$form = $this->createForm(CataloguechantierType::class, $categorie);
		if ($request->getMethod() == 'POST'){
			$form->handleRequest($request);
			$categorie->setServicetext($service);
			if($form->isValid()){
				$em->flush();
				$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
			}else{
				$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
			}
			return $this->redirect($this->generateUrl('users_adminuser_catalogue_chantier_pro'));
		}
		$liste_cours = $em->getRepository(Produit::class)
					      ->findAll();
		$liste_oldrecommandation = $em->getRepository(Recommandation::class)
					                   ->findBy(array('cataloguechantier'=>$categorie));
		return $this->render('Theme/Users/Adminuser/Cataloguechantier/updaterocommandation.html.twig',
		array('categorie'=>$categorie,'liste_cours'=>$liste_cours,'liste_oldrecommandation'=>$liste_oldrecommandation));
	}else{
		echo 'Echec ! Une erreur a été rencontrée.';
		exit;
	}
}

public function assossiationmetier(Cataloguechantier $catalogue, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['recommandation']))
	{
		$tabproduit = explode(',',$_POST['recommandation']);
		
		$liste_cours = $em->getRepository(Produit::class)
	                      ->findBy(array('id'=>$tabproduit));
		if(count($liste_cours) > 0)
		{
			$liste_recommandation = $em->getRepository(Recommandation::class)
					                   ->findBy(array('cataloguechantier'=>$catalogue));
			foreach($liste_recommandation as $oldrecommandation)
			{
				$em->remove($oldrecommandation);
			}
			$em->flush();
			
			foreach($liste_cours as $cours)
			{
				$recommandation = new Recommandation();
				$recommandation->setProduit($cours);
				$recommandation->setCataloguechantier($catalogue);
				$em->persist($recommandation);
				$em->flush();
			}
			$this->get('session')->getFlashBag()->add('information','Mise à jour des recommandations effectuée avec succès !');
		}
	}
	return $this->redirect($this->generateUrl('users_adminuser_catalogue_chantier_pro'));
}

public function searchrecommandations(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();

	$liste_categorie = $em->getRepository(Cataloguechantier::class)
	                 ->myfindAll();
	return $this->render('Theme/Produit/Produit/Cataloguechantier/searchrecommandations.html.twig', 
	array('liste_categorie'=>$liste_categorie));
}

public function updaterecommandations(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	
	$liste_recommandation = new \Doctrine\Common\Collections\ArrayCollection();
	if(isset($_POST['list']))
	{
		$tabmetier = explode('-',$_POST['list']);
		$liste_recommandation = $em->getRepository(Recommandation::class)
					                   ->findBy(array('cataloguechantier'=>$tabmetier));
	}
	
	if(count($liste_recommandation) > 0 and $this->getUser() != null)
	{
		
		$old_recommandation = $em->getRepository(Animationproduit::class)
					            ->findBy(array('recommander'=>1,'user'=>$this->getUser()));
		foreach($old_recommandation as $old_re)
		{
			$em->remove($old_re);
		}
		$em->flush();
		
		foreach($liste_recommandation as $recommandation)
		{
			$animation = new Animationproduit();
			$animation->setUser($this->getUser());
			$animation->setProduit($recommandation->getProduit());
			$animation->setRecommander(true);
			$em->persist($animation);
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