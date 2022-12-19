<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*ce fichier est une proprietéde Zentsoft, 16 février 2015 (01h04min)--debut du Module utilisateurs
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Continent;
use App\Entity\Produit\Service\Pays;
use App\Form\Produit\Service\PaysType;
use App\Form\Produit\Service\ContinentType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Servicetext\GeneralServicetext;

class ContinentController extends AbstractController
{
public function listecontinent(GeneralServicetext $service)
{
	$continent = new Continent($service);
	$form = $this->createForm(ContinentType::class, $continent);
    $em = $this->getDoctrine()->getManager();
    $liste_continent =$em->getRepository(Continent::class)
                      ->findAll();
    return $this->render('Theme/Users/Adminuser/Localisationuser/listecontinent.html.twig', 
	array('liste_continent'=>$liste_continent,'form' => $form->createView()));
}

public function updatecontinent($id, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = $id;
	}
	
	$continent = $em->getRepository(Continent::class)
					->find($id);

	if($continent != null)
	{
	$form = $this->createForm(new ContinentType, $continent);
	if ($request->getMethod() == 'POST'){
		$form->handleRequest($request);
		$continent->setServiceaccent($service);
		if ($form->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
		}
		return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
	}
	return $this->render('Theme/Users/Adminuser/Localisation/updatecontinent.html.twig',
	array('form'=>$form->createView(),'continent'=>$continent));
	}else{
		echo 'Echec ! Une erreur a été rencontrée.';
		exit;
	}
}

public function supprimercontinent(Continent $continent, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	
	if ($request->getMethod() == 'POST'){
	$formsupp->handleRequest($request);
    if ($formsupp->isValid()){
		$liste_pays = $continent->getPays();
		if(count($liste_pays) > 0){
			$this->get('session')->getFlashBag()->add('information','Action réfusée: Ce continent contient déjà les pays.');
		}else{
			$em->remove($continent);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','continent bien supprimé.');
		}
		return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
	}
	}
    $this->get('session')->getFlashBag()->add('supprime_continent',$continent->getId());
	$this->get('session')->getFlashBag()->add('supprime_continent',$continent->getNom());
	return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
}

public function payscontinent($page, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $continent = new Continent($service);
	$form = $this->createForm(ContinentType::class, $continent);
	
	$pays = new Pays($service);
	$formpays = $this->createForm(PaysType::class, $pays);
	if($request->getMethod() == 'POST'){
		$form->handleRequest($request);
		if ($form->isValid()){
			$em = $this->getDoctrine()->getManager();
			$em->persist($continent);
			$em->flush();
		$this->get('session')->getFlashBag()->add('information','le continent a été bien enregistrée.');
		}
	}
	
	$liste_continent = $em->getRepository(Continent::class)
	                 ->findAll();
					 
	$liste_pays = $em->getRepository(Pays::class)
	                 ->findPaysPagine($page, 15);
    $formsupp = $this->createFormBuilder()->getForm(); 					 
	return $this->render('Theme/Users/Adminuser/Localisation/payscontinent.html.twig', 
	array('form' => $form->createView(), 'form2'=>$formpays->createView(), 'liste_continent'=>$liste_continent,
	'liste_pays'=>$liste_pays,'formsupp'=>$formsupp->createView(),'page'=>$page,'nombrepage' => ceil(count($liste_pays)/15)));
}



}