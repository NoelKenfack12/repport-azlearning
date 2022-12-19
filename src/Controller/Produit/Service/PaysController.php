<?php
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Continent;
use App\Entity\Produit\Service\Pays;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Produit\Service\PaysType;
use App\Service\Servicetext\GeneralServicetext;
use App\Entity\Administration\Superadmin\Icone;
use App\Entity\Users\User\User;

class PaysController extends AbstractController
{
public function ajouterpays(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$pays = new Pays($service);
    $form2 = $this->createForm(PaysType::class, $pays);

	if ($request->getMethod() == 'POST') {
	$form2->handleRequest($request);
    if ($form2->isValid()){
		if($pays->getDrapeau() !== null)
		{
			$pays->getDrapeau()->setServicefile($service);
		}

		$em->persist($pays);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','le pays a été bien enregistrée.');
	}else{
		$pays->getContinent()->removePay($pays);
		$this->get('session')->getFlashBag()->add('information','Echec, Une erreur a été rencontrée');
	}
	}
	return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
}

public function listepayscontinent(Continent $continent,$page)
{
	if($page < 1){
		return $this->redirect($this->generateUrl('admin_user_localisation'));
	}
	$form = $this->createFormBuilder()->getForm();
	$em = $this->getDoctrine()->getManager();
	$drapeau = $em->getRepository(Icone::class)
	             ->findOneBy(array('nom'=>'drapeau'));
	$liste_pays = $em->getRepository(Pays::class)
	                 ->myfindByContinent($continent->getId(),12, $page);
	return $this->render('Theme/Users/Adminuser/Localisationuser/listepayscontinent.html.twig',
	array('nombrepage' => ceil(count($liste_pays)/12),'continent'=>$continent,'liste_pays'=>$liste_pays,
	'page'=>$page,'drapeau'=>$drapeau,'formsupp'=>$form->createView()));
}

public function modificationpays($id, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = $id;
	}
	
	$pays = $em->getRepository(Pays::class)
					->find($id);
	if($pays != null)
	{
	$form2 = $this->createForm(PaysType::class, $pays);
	$pays->setServicetext($service);
	if($request->getMethod() == 'POST'){
		$form2->handleRequest($request);
		if($pays->getDrapeau() != null)
		{
			$pays->getDrapeau()->setServicefile($service);
		}
		if($form2->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée!');
		}
		return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
	}
	return $this->render('Theme/Users/Adminuser/Localisation/modificationpays.html.twig',
	array('form2'=>$form2->createView(),'pays'=>$pays));
	}else{
		echo 'Echec ! Une erreur a été rencontrée.';
		exit;
	}
}

public function dropcountryuser(Pays $pays, Request $request)
{
	$form = $this->createFormBuilder()->getForm();
	if ($request->getMethod() == 'POST') {
	$form->handleRequest($request);
    if ($form->isValid()){
	$em = $this->getDoctrine()->getManager();
	$liste_user = $em->getRepository(User::class)
	                 ->findBy(array('pays'=>$pays));
	if(count($liste_user) == 0)
	{
		$em->remove($pays);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Action réfusée; ce pays est reservé par les utilisateurs');
	}
	return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
	}
	}
	$this->get('session')->getFlashBag()->add('pays_supp',''.$pays->getId().'');
	$this->get('session')->getFlashBag()->add('pays_supp',''.$pays->getNom().'');
	return $this->redirect($this->generateUrl('users_adminuser_form_pays_continent'));
}

public function serviceaddpays(GeneralServicetext $service, Request $request)
{
	$pays = new Pays();
	$formpays = $this->createForm(PaysType::class, $pays);
	if ($request->getMethod() == 'POST'){
    $formpays->handleRequest($request);
    if($formpays->isValid()){
		$pays->getDrapeau()->setServicefile($service);
		$em = $this->getDoctrine()->getManager();
		$liste_pays = $em->getRepository(Pays::class)
						->findAll();
		if(count($liste_pays) == 0)
		{
			$em->persist($pays);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','le pays a été bien enregistrée.');
		}else{
			$pays->getContinent()->removePay($pays);
			$this->get('session')->getFlashBag()->add('information','Action réfusée!!! Les données issus de cette interface ne peuvent plus être validées.');
		}
	}
	}
	return $this->redirect($this->generateUrl('general_service_pays_continent'));
}

public function autosearchcountry($position = 'other')
{
	$em = $this->getDoctrine()->getManager();
	$liste_pays = $em->getRepository(Pays::class)
	                 ->myFindBy(250);
	return $this->render('Theme/Users/Localisationuser/Pays/autosearchcountry.html.twig', 
	array('liste_pays'=>$liste_pays,'position'=>$position));
}
}
