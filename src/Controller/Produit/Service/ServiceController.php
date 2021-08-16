<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2016
*/
namespace App\Controller\Produit\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Produit\Service\ServiceType;
use App\Form\Produit\Service\ServiceeditType;
use App\Entity\Produit\Service\Service;
use App\Entity\Produit\Service\Produitformation;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Produit\Service\Intervention;
use App\Form\Produit\Service\CommentaireblogType;
use App\Form\Produit\Service\InterventionType;
use App\Form\Produit\Service\EvenementType;
use App\Form\Produit\Service\FormationeditType;
use App\Form\Produit\Service\ImgevenementType;
use App\Entity\Produit\Service\Evenement;
use App\Entity\Produit\Service\Imgevenement;
use App\Entity\General\Template\Recherche;
use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Service\Apropos;
use App\Entity\Produit\Service\Infoentreprise;
use App\Form\Produit\Service\InfoentrepriseeditType;
use App\Entity\Produit\Produit\Souscategorie;
use App\Entity\Produit\Service\Detailinfoentreprise;
use App\Form\Produit\Service\DetailinfoentrepriseType;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Entity\Produit\Produit\Pratiquechapitre;
use App\Entity\Produit\Produit\Supportchapitre;
use App\Entity\Produit\Produit\Exercicepartie;
use App\Entity\Users\User\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users\User\Imgslide;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ServiceController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function nouveauservice(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$nosservice = new Service($service);
    $form = $this->createForm(ServiceType::class, $nosservice);
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
	$nosservice->setUser($this->getUser());
	
	if($nosservice->getImgservice() !== null)
	{
		$nosservice->getImgservice()->setServicetext($service);
	}
	if($nosservice->getImgservicesecond() !== null)
	{
		$nosservice->getImgservicesecond()->setServicetext($service);
	}
	$nosservice->setPrincipal(true);
	
	$valid  = false;
	$datetext = '00-00-0000';
	if (isset($_POST['jour']) and isset($_POST['mois']) and isset($_POST['annee'])){
		$date = ''.$_POST['jour'].'/'.$_POST['mois'].'/'.$_POST['annee'].'';
		$datetext = ''.$_POST['jour'].'-'.$_POST['mois'].'-'.$_POST['annee'].'';
		if($service->mydate($date))
		{
			$nosservice->setDatepicket($date);
			$nosservice->setDatebegin($datetext);
			$valid = true;
		}
	}

	$datetextend = '00-00-0000';
	if (isset($_POST['jourend']) and isset($_POST['moisend']) and isset($_POST['anneeend']) and $valid == true){
		$date = ''.$_POST['jourend'].'/'.$_POST['moisend'].'/'.$_POST['anneeend'].'';
		$datetextend = ''.$_POST['jourend'].'-'.$_POST['moisend'].'-'.$_POST['anneeend'].'';
		if($service->mydate($date))
		{
			$nosservice->setDatefin($datetextend);
			$valid = true;
		}
	}else{
		$valid = false;
	}
	
	if(isset($_POST['allposteservice']) and $valid == true)
	{
		$tabcours = explode('$$$$$$', $_POST['allposteservice']);
		$liste_cours = $em->getRepository(Produit::class)
	                        ->findBy(array('id'=>$tabcours));
		if(count($liste_cours) > 0)
		{
			foreach($liste_cours as $cours)
			{
				$nosservice->addProduit($cours);
				
				$produitformation = new Produitformation();
				$produitformation->setProduit($cours);
				$produitformation->setService($nosservice);
				$em->persist($produitformation);
			}
		}else{
			$valid = false;
		}
	}else{
		$valid = false;
	}
			
    if ($form->isValid() and $valid == true and $_POST['dureeacces'] and $_POST['dureeacces'] != 0){
		$em->persist($nosservice);
		
		if(isset($_POST['dureeminute']))
		{
			$nosservice->setDureeminute($_POST['dureeminute']);
		}
		if(isset($_POST['dureeseconde']))
		{
			$nosservice->setDureeseconde($_POST['dureeseconde']);
		}
		
		$nosservice->setDureeacces($_POST['dureeacces']);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
		return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$nosservice->getId())));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée');
	}
	}
	$liste_service = $em->getRepository(Service::class)
	                    ->findBy(array('principal'=>1));
	return $this->render('Theme/Users/Adminuser/Service/nouveauservice.html.twig',
	array('form'=>$form->createView(),'liste_service'=>$liste_service,'formsupp'=>$formsupp->createView()));
}

public function nouvellecompetition(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$nosservice = new Service($service);
    $form = $this->createForm(ServiceeditType::class, $nosservice);
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST'){
		$form->handleRequest($request);
		$nosservice->setUser($this->getUser());
		if($nosservice->getImgservice() !== null)
		{
			$nosservice->getImgservice()->setServicetext($service);
		}
		$nosservice->setThemeforum(true);

		if ($form->isValid()){
			$em->persist($nosservice);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$nosservice->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée');
		}
	}
	$liste_service = $em->getRepository(Service::class)
	                    ->findBy(array('themeforum'=>1));
	return $this->render('Theme/Users/Adminuser/Competition/nouvellecompetition.html.twig',
	array('form'=>$form->createView(),'liste_service'=>$liste_service,'formsupp'=>$formsupp->createView()));
}

public function addnewservice(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$info = new Infoentreprise($service);
	$form = $this->createForm(InfoentrepriseeditType::class, $info);
	$formsupp = $this->createFormBuilder()->getForm();

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$info->setUser($this->getUser());
		if($info->getImginfoentreprise() !== null)
		{
			$info->getImginfoentreprise()->setServicetext($service);
		}
		$info->setValeur(true);
		if($form->isValid()){
			$em->persist($info);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Publication enregistrée avec succès.');
		}else{

			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée.');

		}
	}
	$allinfo = $em->getRepository(Infoentreprise::class)
	                      ->findBy(array('valeur'=>true));
    return $this->render('Theme/Users/Adminuser/Servicestruct/addnewservice.html.twig',
	array('form'=>$form->createView(),'allinfo'=>$allinfo,'formsupp'=>$formsupp->createView()));
}

public function modifierservice(Service $nosservice, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $form = $this->createForm(FormationeditType::class, $nosservice);
	$formsupp = $this->createFormBuilder()->getForm();

	$nosservice->setServicetext($service);
	$evenement = new Evenement($service);
    $formeven = $this->createForm(EvenementType::class, $evenement);
	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
	$nosservice->setUser($this->getUser());
	if($nosservice->getImgservice() !== null)
	{
		$nosservice->getImgservice()->setServicetext($service);
	}
	if($nosservice->getImgservicesecond() !== null)
	{
		$nosservice->getImgservicesecond()->setServicetext($service);
	}
	$nosservice->setPrincipal(true);
	
	$valid  = false;
	$datetext = '00-00-0000';
	if (isset($_POST['jour']) and isset($_POST['mois']) and isset($_POST['annee'])){
		$date = ''.$_POST['jour'].'/'.$_POST['mois'].'/'.$_POST['annee'].'';
		$datetext = ''.$_POST['jour'].'-'.$_POST['mois'].'-'.$_POST['annee'].'';
		if($service->mydate($date))
		{
			$nosservice->setDatepicket($date);
			$nosservice->setDatebegin($datetext);
			$valid = true;
		}
	}

	$datetextend = '00-00-0000';
	if (isset($_POST['jourend']) and isset($_POST['moisend']) and isset($_POST['anneeend']) and $valid == true){
		$date = ''.$_POST['jourend'].'/'.$_POST['moisend'].'/'.$_POST['anneeend'].'';
		$datetextend = ''.$_POST['jourend'].'-'.$_POST['moisend'].'-'.$_POST['anneeend'].'';
		if($service->mydate($date))
		{
			$nosservice->setDatefin($datetextend);
			$valid = true;
		}
	}else{
		$valid = false;
	}
	
	if(isset($_POST['allposteservice']) and $valid == true)
	{
		$tabcours = explode('$$$$$$', $_POST['allposteservice']);
		$liste_cours = $em->getRepository(Produit::class)
	                        ->findBy(array('id'=>$tabcours));
		foreach($liste_cours as $cours)
		{
			$oldformation = $em->getRepository(Produitformation::class)
	                           ->findOneBy(array('produit'=>$cours,'service'=>$nosservice));
			if($oldformation == null)
			{				
				$produitformation = new Produitformation();
				$produitformation->setProduit($cours);
				$produitformation->setService($nosservice);
				$em->persist($produitformation);
			}

			$exist = false;
			foreach($nosservice->getProduits() as $oldp)
			{
				if($oldp == $cours)
				{
					$exist = true;
					break;
				}
			}
			if($exist == false)
			{
				$nosservice->addProduit($cours);
			}
		}
	}else{
		$valid = false;
	}
			
    if ($form->isValid() and $valid == true and $_POST['dureeacces'] and $_POST['dureeacces'] != 0){
		if(isset($_POST['dureeminute']))
		{
			$nosservice->setDureeminute($_POST['dureeminute']);
		}
		if(isset($_POST['dureeseconde']))
		{
			$nosservice->setDureeseconde($_POST['dureeseconde']);
		}
		
		$em->persist($nosservice);
		$nosservice->setDureeacces($_POST['dureeacces']);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
		return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$nosservice->getId())));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée');
	}
	}
	$liste_service = $em->getRepository(Service::class)
	                    ->findBy(array('principal'=>1));
						
	return $this->render('Theme/Users/Adminuser/Service/modifierservice.html.twig',
	array('form'=>$form->createView(),'formsupp'=>$formsupp->createView(),'liste_service'=>$liste_service,
	'service'=>$nosservice,'formeven'=>$formeven->createView()));
}

public function removecours(Service $nosservice, Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$nosservice->removeProduit($produit);

	$oldformation = $em->getRepository(Produitformation::class)
					   ->findOneBy(array('produit'=>$produit,'service'=>$nosservice));
	if($oldformation != null)
	{				
		$em->remove($oldformation);
	}	
	$nosservice->setServicetext($service);
	$em->flush();
	
	$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
	return $this->redirect($this->generateUrl('users_adminuser_modifier_un_service',array('id'=>$nosservice->getId())));
}

public function modificationservice(Infoentreprise $info, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$form = $this->createForm(InfoentrepriseeditType::class, $info);
	$formsupp = $this->createFormBuilder()->getForm();

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$info->setUser($this->getUser());
		if($info->getImginfoentreprise() !== null)
		{
		$info->getImginfoentreprise()->setServicetext($service);
		}
		$info->setValeur(true);
		if($form->isValid()){
			$em->persist($info);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Modification effectuée succès.');
			return $this->redirect($this->generateUrl('users_adminusers_add_new_service'));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée.');
		}
	}

	$detailinfo = new Detailinfoentreprise();
	$formdetail = $this->createForm(DetailinfoentrepriseType::class, $detailinfo);
	$allinfo = $em->getRepository(Infoentreprise::class)
	                      ->findBy(array('valeur'=>true));
    return $this->render('Theme/Users/Adminuser/Servicestruct/modificationservice.html.twig',
	array('form'=>$form->createView(),'allinfo'=>$allinfo,'formsupp'=>$formsupp->createView(),'info'=>$info,
	'formdetail'=>$formdetail->createView()));
}

public function modifiercompetition(Service $nosservice, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $form = $this->createForm(ServiceeditType::class, $nosservice);
	$formsupp = $this->createFormBuilder()->getForm();

	$evenement = new Evenement($service);
    $formeven = $this->createForm(EvenementType::class, $evenement);

	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
	$nosservice->setUser($this->getUser());
	if($nosservice->getImgservice() !== null)
	{
		$nosservice->getImgservice()->setServicetext($service);
	}

	$nosservice->setThemeforum(true);
	
    if ($form->isValid()){
		$em->persist($nosservice);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
		return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$nosservice->getId())));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée, Choisissez un type et retransmettez le formulaire!');
	}
	}
	$liste_service = $em->getRepository(Service::class)
	                    ->findBy(array('themeforum'=>1));
						
	return $this->render('Theme/Users/Adminuser/Competition/modifiercompetition.html.twig',
	array('form'=>$form->createView(),'formsupp'=>$formsupp->createView(),'liste_service'=>$liste_service,
	'service'=>$nosservice,'formeven'=>$formeven->createView()));
}

public function addevenement(Service $nosservice, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$evenement = new Evenement($service);
    $formeven = $this->createForm(EvenementType::class, $evenement);
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST'){
		$formeven->handleRequest($request);
		$evenement->setUser($this->getUser());
		if($evenement->getImgevenement() !== null)
		{
			$evenement->getImgevenement()->setServicetext($service);
		}
		$evenement->setService($nosservice);
		if ($formeven->isValid()){
			$em->persist($evenement);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée !');
		}
	}
	$liste_even = $em->getRepository(Evenement::class)
	                    ->findBy(array('service'=>$nosservice),array('date'=>'asc'));
						
	$liste_cours = $em->getRepository(Produitformation::class)
					   ->findBy(array('service'=>$nosservice));
					   				   
	return $this->render('Theme/Users/Adminuser/Service/addevenement.html.twig',
	array('formeven'=>$formeven->createView(),'service'=>$nosservice,'liste_even'=>$liste_even,
	'liste_cours'=>$liste_cours,'formsupp'=>$formsupp->createView()));
}

public function supprimevenement(Evenement $even, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm();
	$service = $even->getService();

	if ($request->getMethod() == 'POST') {
	$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$em->remove($even);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
		}
	}else{
		$this->get('session')->getFlashBag()->add('supprime_prod',$even->getId());
		$this->get('session')->getFlashBag()->add('supprime_prod',$even->getNom());
	}
	return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$service->getId())));
}

public function supprimerservice(Service $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$liste_evenement = $em->getRepository(Evenement::class)
							->findBy(array('service'=>$service));
			if(count($liste_evenement) == 0)
			{
				$em->remove($service);
				$em->flush();
				$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			}else{
				$this->get('session')->getFlashBag()->add('information','Action réfusée ! Supprimez les articles liés à cette offre de formation.');
			}
		}
	}else{
		$this->get('session')->getFlashBag()->add('supprime_prod',$service->getId());
		$this->get('session')->getFlashBag()->add('supprime_prod',$service->getNom());
	}
	return $this->redirect($this->generateUrl('users_adminuser_ajouter_nouveau_service'));
}

public function supprimervaleurformation(Infoentreprise $info, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
    
	if ($request->getMethod() == 'POST') {
    $formsupp->handleRequest($request);
    if ($formsupp->isValid()){
		$details = $info->getDetailinfoentreprises();
		foreach($details as $detail)
		{
			$em->remove($detail);
		}
		$em->remove($info);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
		return $this->redirect($this->generateUrl('users_adminusers_add_new_service'));
	}
	}
    $this->get('session')->getFlashBag()->add('supprime_apropos',$info->getId());
	$this->get('session')->getFlashBag()->add('supprime_apropos',$info->getTitre());
	return $this->redirect($this->generateUrl('users_adminusers_add_new_service'));
}

public function supprimercompetitionAction(Service $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST'){
	$formsupp->handleRequest($request);
	if ($formsupp->isValid()){
		$liste_evenement = $em->getRepository(Evenement::class)
						->findBy(array('service'=>$service));
		$liste_dom = $em->getRepository(Produit::class)
						->findBy(array('equipedom'=>$service));
		$liste_ext = $em->getRepository(Produit::class)
						->findBy(array('equipeext'=>$service));
		$liste_competition = $em->getRepository(Produit::class)
						        ->findBy(array('competition'=>$service));
		if(count($liste_evenement) == 0 and count($liste_dom) == 0 and count($liste_ext) == 0 and count($liste_competition) == 0)
		{
			$em->remove($service);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Action réfusée! Supprimez les articles liés à cette compétition.');
		}
	}
	}else{
	$this->get('session')->getFlashBag()->add('supprime_prod',$service->getId());
	$this->get('session')->getFlashBag()->add('supprime_prod',$service->getNom());
	}
	return $this->redirect($this->generateUrl('users_adminuser_ajouter_nouvelle_competition_user'));
}

public function presentation($id = 0, GeneralServicetext $servicetext)
{
	$em = $this->getDoctrine()->getManager();
	$liste_service = $em->getRepository(Service::class)
	                    ->myfindAll();
	if($id != 0)
	{
		$service = $em->getRepository(Service::class)
	                  ->find($id);
		$newliste = new \Doctrine\Common\Collections\ArrayCollection();
		if($service != null)
		{
		$newliste[] = $service;
		}
		foreach($liste_service as $courantservice)
		{
			if($service != $courantservice)
			{
				$newliste[] = $courantservice;
			}
		}
		$liste_service = $newliste;
	}else{
		$compt = 0;
	    foreach($liste_service as $ser)
		{
			if($compt == 0)
			{
				$service = $ser;
				break;
			}
		}
	}
	if($service != null)
	{
		$repository = $em->getRepository(Apropos::class);
		$liste_team = $repository->FindBy(array('particulier'=>true));
		$servicetext = $this->container->get('general_service.servicetext');
		$team_select = $servicetext->selectEntities($liste_team, 3);
		$liste_envent = $em->getRepository(Evenement::class)
						->findBy(array('service'=>$service),array('rang'=>'asc'));
		return $this->render('Theme/Produit/Service/Service/presentation.html.twig',
		array('liste_envent'=>$liste_envent,'service'=>$service,'liste_service'=>$liste_service,'team_select'=>$team_select));
	}else{
		return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
	}
}

public function nosformations($page)
{
	$em = $this->getDoctrine()->getManager();
	$liste_formation = $em->getRepository(Service::class)
	                      ->listeformation($page,6);
						  
	return $this->render('Theme/Produit/Service/Service/nosformations.html.twig', 
	array('nombrepage' => ceil(count($liste_formation)/6),'page'=>$page));
}

public function listeformation($page)
{
	if(isset($_GET['page']))
	{
		$page = $_GET['page'];
	}else{
		$page = $page;
	}
	
	$em = $this->getDoctrine()->getManager();
	$liste_formation = $em->getRepository(Service::class)
	                      ->listeformation($page,6);
	foreach($liste_formation as $formation)
	{
		$formation->setEm($em);
	}
	return $this->render('Theme/Produit/Service/Service/listeformation.html.twig',
	array('nombrepage' => ceil(count($liste_formation)/6),'page'=>$page,'liste_formation'=>$liste_formation));
}

public function affichearticle(Service $service)
{
	$em = $this->getDoctrine()->getManager();
	$recherche = new Recherche();
	$formBuilder = $this->createFormBuilder($recherche);
	$formBuilder
              ->add('donnee', 'text',array('attr'=>array('class'=>'textbox','placeholder'=>'Lancer une recherche','type'=>'search')));
	$formrecher = $formBuilder->getForm();
	
	$liste_even = $em->getRepository(Evenement::class)
	                 ->findBy(array('service'=>$service),array('rang'=>'desc'));
	$liste_com = $em->getRepository(Commentaireblog::class)
	                 ->findBy(array('service'=>$service),array('date'=>'desc'));
	
	$liste_categorie = $em->getRepository(Categorie::class)
	                      ->findAll();
					 
	$comment = new Commentaireblog();
    $form = $this->createForm(CommentaireblogType::class, $comment);
	return $this->render('Theme/Produit/Service/Service/affichearticle.html.twig', 
	array('article'=>$service,'liste_categorie'=>$liste_categorie,'form'=>$form->createView(),
	'formrecher'=>$formrecher->createView(),'liste_even'=>$liste_even,'liste_com'=>$liste_com));
}

public function ajoutersujetforum(Service $nosservice, $page, $addProduct, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$comment = new Commentaireblog();
    $form = $this->createForm(CommentaireblogType::class, $comment);
	if ($request->getMethod() == 'POST'){
		$form->handleRequest($request);

		$comment->setService($nosservice);
		if ($form->isValid() and $this->getUser() != null){
			$comment->setUser($this->getUser());
			$em->persist($comment);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
		}else{
			if($this->getUser() == null)
			{
				$this->get('session')->getFlashBag()->add('information','Une erreur ! vous n\'êtes pas connectez !');
			}else{
				$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !');
			}
		}
	}
	$sujet_forum = $em->getRepository(Commentaireblog::class)
	                  ->findSujetForum($nosservice->getId(),$page,10);
				   
	if($nosservice->getThemeforum() == true)
	{
		return $this->render('Theme/Produit/Service/Forum/ajoutersujetforum.html.twig',
		array('nosservice'=>$nosservice,'form'=>$form->createView(), 'nombrepage' => ceil(count($sujet_forum)/10),
		'page'=>$page,'sujet_forum'=>$sujet_forum,'addProduct'=>$addProduct));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée ! Aucun thème du forum choisi.');
		return $this->redirect($this->generateUrl('produit_service_forum_site'));
	}
}

public function interventionsujet(Commentaireblog $comment, $page, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$intervention = new Intervention();
    $form = $this->createForm(InterventionType::class, $intervention);
	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);

	$intervention->setCommentaireblog($comment);
    if ($form->isValid() and $this->getUser() != null){
		$intervention->setUser($this->getUser());
		$em->persist($intervention);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
	}else{
		if($this->getUser() == null)
		{
			$this->get('session')->getFlashBag()->add('information','Une erreur ! vous n\'êtes pas connectez !');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !');
		}
	}
	}
	$liste_intervention = $em->getRepository(Intervention::class)
	                         ->findInterventionSujet($comment->getId(),$page,10);

	return $this->render('Theme/Produit/Service/Forum/interventionsujet.html.twig',
	array('comment'=>$comment,'form'=>$form->createView(), 'nombrepage' => ceil(count($liste_intervention)/10), 
	'page'=>$page,'liste_intervention'=>$liste_intervention));
}

public function deleteintervention(Intervention $intervention)
{
	$comment = $intervention->getCommentaireblog();
	$em = $this->getDoctrine()->getManager();
	if($this->isGranted('ROLE_GESTION') or $this->getUser() == $comment->getUser()){
		$em->remove($intervention);
		$em->flush();
	}
	return $this->redirect($this->generateUrl('produit_service_interventions_sujets_courant',array('id'=>$comment->getId())));
}

public function banniereforum(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$allslide = $em->getRepository(Imgslide::class)
	               ->findSlideAnnonce();
	$slideaccueil = $service->selectEntity($allslide);
	
	$categorie_forum = $em->getRepository(Service::class)
	                      ->findAllThemeForum();
						  
	
	return $this->render('Theme/Produit/Service/Forum/banniereforum.html.twig', 
	array('slideaccueil'=>$slideaccueil,'categorie_forum'=>$categorie_forum));
}

public function rechercheforum($donnee, $page, Request $request)
{
	$em = $this->getDoctrine()->getManager();

	$liste_sujet = $em->getRepository(Commentaireblog::class)
						->searchSujetForum($_POST["donnee"], $page, 10);
	return $this->render('Theme/Produit/Service/Forum/rechercheforum.html.twig', 
	array('liste_sujet'=>$liste_sujet,'nombrepage' => ceil(count($liste_sujet)/10),'recherche'=>$recherche,'page'=>$page));
}

public function supprimercommentaire(Commentaireblog $com)
{
	$service = $com->getService();
	$em = $this->getDoctrine()->getManager();
	if($this->isGranted('ROLE_GESTION') or $this->getUser() == $com->getUser()){
		if(count($com->getInterventions()) == 0)
		{
			$em->remove($com);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès !!!');
		}else{
			$this->get('session')->getFlashBag()->add('information','Vous devez d\'abord supprimer les réponses à ce sujet.');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Vous n\'avez pas le droit d\'effectuer cette action');
	}
	return $this->redirect($this->generateUrl('produit_service_ajouter_commentaire_article_user',array('id'=>$service->getId())));
}

public function ajouterimgservice(Service $service, GeneralServicetext $servicetext, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$imgservice = new Imgevenement();
	$form = $this->createForm(ImgevenementType::class, $imgservice);
	$formsupp = $this->createFormBuilder()->getForm();
	
	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$imgservice->setUser($this->getUser());
		$imgservice->setService($service);
		$imgservice->setServicetext($servicetext);
		if($form->isValid()){
			$em->persist($imgservice);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Publication enregistrée avec succès.');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée. Vérifiez la taille du fichié');
		}
	}
	$allimage = $em->getRepository(Imgevenement::class)
	                      ->findBy(array('service'=>$service));
    return $this->render('Theme/Users/Adminuser/Service/ajouterimgservice.html.twig',
	array('form'=>$form->createView(),'allimage'=>$allimage,'formsupp'=>$formsupp->createView(),'service'=>$service));
}

public function supprimerimgservice(Imgevenement $image, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	$service = $image->getService();
    
	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
			$em->remove($image);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_ajouter_image_service_courant',array('id'=>$service->getId())));
		}
	}
    $this->get('session')->getFlashBag()->add('supprime_apropos',$image->getId());
	$this->get('session')->getFlashBag()->add('supprime_apropos',$image->getService()->getNom());
	return $this->redirect($this->generateUrl('users_adminuser_ajouter_image_service_courant',array('id'=>$service->getId())));
}

public function modifierevenement(Evenement $event, GeneralServicetext $servicetext, Request $request)
{
	$em = $this->getDoctrine()->getManager();
    $formeven = $this->createForm(EvenementType::class, $event);
	
	if ($request->getMethod() == 'POST'){
		$formeven->bind($request);
		if($event->getImgevenement() !== null)
		{
		$event->getImgevenement()->setServicetext($servicetext);
		}
		if ($formeven->isValid()){
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
			return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement',array('id'=>$event->getService()->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée, Vérifier le formulaire');
		}
	}
	$liste_evenement = $em->getRepository(Evenement::class)
	                    ->findBy(array('service'=>$event->getService()), array('rang'=>'asc'));
	return $this->render('Theme/Users/Adminuser/Service/modifierevenement.html.twig',
	array('formeven'=>$formeven->createView(),'event'=>$event,'liste_evenement'=>$liste_evenement));
}

public function formateurstruct($page)
{
	$em = $this->getDoctrine()->getManager();
	$liste_formateur = $em->getRepository(User::class)
	                      ->listeformateur($page,12);
	return $this->render('Theme/Produit/Service/Service/formateurstruct.html.twig', 
	array('nombrepage' => ceil(count($liste_formateur)/12),'page'=>$page));
}

public function listeformateur($page)
{
	if(isset($_GET['page']))
	{
	 $page = $_GET['page'];
	}else{
	 $page = $page;
	}
	
	$em = $this->getDoctrine()->getManager();
	$liste_formateur = $em->getRepository(User::class)
	                      ->listeformateur($page,12);
	return $this->render('Theme/Produit/Service/Service/listeformateur.html.twig',
	array('nombrepage' => ceil(count($liste_formateur)/12),'page'=>$page,'liste_formateur'=>$liste_formateur));
}

public function accueilforum()
{
	$em = $this->getDoctrine()->getManager();
	$categorie_forum = $em->getRepository(Souscategorie::class)
	                      ->souscategorieforum();
						  
	foreach($categorie_forum as $categorie)
	{
		$categorie->setEm($em);
	}		
	return $this->render('Theme/Produit/Service/Forum/accueilforum.html.twig',
	array('categorie_forum'=>$categorie_forum));
}

public function presentationformation(Service $service)
{
	$em = $this->getDoctrine()->getManager();
	$liste_cours = $service->getProduits();
	$nbvideo = 1;
	$nbsupport = 0;
	$nbpratique = 0;
	$nbexercice = 0;
	foreach($liste_cours as $cours)
	{
		$liste_video = $em->getRepository(Chapitrecours::class)
	                      ->listechapitrecours($cours->getId());
		$nbvideo = $nbvideo + count($liste_video) + 1;
		
		$liste_support = $em->getRepository(Supportchapitre::class)
	                      ->myFindByCours($cours->getId());
		$nbsupport = $nbsupport + count($liste_support);
		
		$liste_tp = $em->getRepository(Pratiquechapitre::class)
	                      ->myFindByCours($cours->getId());
		$nbpratique = $nbpratique + count($liste_tp);
		
		$liste_exercice = $em->getRepository(Exercicepartie::class)
	                      ->myFindByCours($cours->getId());
		$nbexercice = $nbexercice + count($liste_exercice);
	}
	
	$liste_diapo = $em->getRepository(Infoentreprise::class)
				  ->findBy(array('principal'=>1,'valeur'=>0), array('rang'=>'desc'));
				  
	return $this->render('Theme/Produit/Service/Service/presentationformation.html.twig', 
	array('service'=>$service,'nbvideo'=>$nbvideo,'liste_diapo'=>$liste_diapo,'nbsupport'=>$nbsupport,
	'nbpratique'=>$nbpratique,'nbexercice'=>$nbexercice));
}

public function addformationpanier(Service $service, GeneralServicetext $serviceadd)
{
	if(isset($_POST['_typedeformation']) and isset($_POST['_password']))
	{
		$em = $this->getDoctrine()->getManager();
		if($_POST['_typedeformation'] == 1)
		{
			$montant = $service->getNprix();
			$value = 1;
		}else{
			$montant = $service->getNprixoff();
			$value = 0;
		}
		//$nbjour = $this->date->diff(new \Datetime())->days;
		if($this->getUser()->getSoldeprincipal() >= $montant)
		{
			if($this->getUser()->getPassword() == $_POST['_password'])
			{
				$oldpanier = $em->getRepository(Panier::class)
								->findOneBy(array('user'=>$this->getUser(),'service'=>$service));
				if($oldpanier == null)//s'il n'a jamais été inscrit à cette formation, on l'inscrit
				{
					$this->getUser()->setSoldeprincipal($this->getUser()->getSoldeprincipal() - $montant);
					$panier = new Panier();
					$panier->setUser($this->getUser());
					$panier->setService($service);
					$panier->setMontantttc($montant);
					$em->persist($panier);
					foreach($service->getProduits() as $produit)
					{
						$produitpanier = new Produitpanier();
						$produitpanier->setPanier($panier);
						$produitpanier->setProduit($produit);
						$produitpanier->setQuantite(1);
						$em->persist($produitpanier);
						$produit->setNbcertificat($produit->getNbcertificat() + 1);
					}
					$service->setNbcertificat($service->getNbcertificat() + 1);
					$this->get('session')->getFlashBag()->add('information','Inscription à la formation effectuée avec succès !');
					$em->flush();
					
					
					//envoie d'email
						$siteweb = $this->params->get('siteweb');
						$sitename = $this->params->get('sitename');
						$emailadmin = $this->params->get('emailadmin');
						if($serviceadd->email($emailadmin))
						{
							$response = $this->_servicemail->sendNotifEmail(
								'Team '.$sitename, //Nom du destinataire
								$emailadmin, //Email Destinataire
								$this->getUser()->name(30).' vient de s\'inscrire à la formation '.$service->getNom().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de s\'inscrire à la formation '.$service->getNom().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/accueil/user/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);

						}
						
						foreach($service->getProduits() as $produit)
						{
							if($serviceadd->email($produit->getUser()->getUsername()))
							{
								$response = $this->_servicemail->sendNotifEmail(
									$produit->getUser()->name(50), //Nom du destinataire
									$produit->getUser()->getUsername(), //Email Destinataire
									$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
									$this->getUser()->name(30).' vient de s\'inscrire au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
									'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
									 ''  //Lien à suivre
								);
							}
							sleep(2);
						}
						
						if($serviceadd->email($this->getUser()->getUsername()))
						{
							$response = $this->_servicemail->sendNotifEmail(
								$this->getUser()->name(50), //Nom du destinataire
								$this->getUser()->getUsername(), //Email Destinataire
								'Votre inscription à la formation '.$service->getNom().' a été effectuée avec succès sur '.$sitename.' !', //Objet de l'email
								'Votre inscription à la formation '.$service->getNom().' a été effectuée avec succès sur '.$sitename.' !', //Grand Titre de l'email
								'Accédez à votre bableau de bord pour suivre votre progression à cette formation .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);
						}
						
				}else{
					if($oldpanier->getValide() == true)//s'il est encore inscrit
					{
						$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous êtes déjà inscrit à cette formation.');
					}else{  //si son inscription a expiré.
						$this->getUser()->setSoldeprincipal($this->getUser()->getSoldeprincipal() - $montant);
						$oldpanier->setDate(new \Datetime());
						$oldpanier->setValide(true);
						$oldpanier->setMontantttc($montant);
						$em->flush();
						
						//envoie d'email
						$siteweb = $this->params->get('siteweb');
						$sitename = $this->params->get('sitename');
						$emailadmin = $this->params->get('emailadmin');
						if($serviceadd->email($emailadmin))
						{
							$response = $this->_servicemail->sendNotifEmail(
								'Team '.$sitename, //Nom du destinataire
								$emailadmin, //Email Destinataire
								$this->getUser()->name(30).' vient de renouveler son inscription à la formation '.$service->getNom().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de renouveler son inscription à la formation '.$service->getNom().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/accueil/user/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);
						}
						
						foreach($service->getProduits() as $produit)
						{
							if($serviceadd->email($produit->getUser()->getUsername()))
							{

								$response = $this->_servicemail->sendNotifEmail(
									$produit->getUser()->name(50), //Nom du destinataire
									$produit->getUser()->getUsername(), //Email Destinataire
									$this->getUser()->name(30).' vient de renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
									$this->getUser()->name(30).' vient de renouveler son inscription au cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
									'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$oldpanier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
									 ''  //Lien à suivre
								);
							}
							sleep(2);
						}
						
						if($serviceadd->email($this->getUser()->getUsername()))
						{
							$response = $this->_servicemail->sendNotifEmail(
								$this->getUser()->name(50), //Nom du destinataire
								$this->getUser()->getUsername(), //Email Destinataire
								'Votre inscription à la formation '.$service->getNom().' a été renouvelée avec succès sur '.$sitename.' !', //Objet de l'email
								'Votre inscription à la formation '.$service->getNom().' a été renouvelée avec succès sur '.$sitename.' !', //Grand Titre de l'email
								'Accédez à votre bableau de bord pour suivre votre progression à cette formation .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);

						}
					}
				} 
			}else{
				$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Le mot de passe que vous avez entré est invalid.');
			}
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous n\'avez pas assez de fond pour souscrire à cette formation. Veuillez ajouter les fonds à votre compte Az.');
		}
		$oldpanier = $em->getRepository(Panier::class)
						->findOneBy(array('user'=>$this->getUser(),'payer'=>0));
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Toutes les données n\'ont pas été reçu.');
	}
	return $this->redirect($this->generateUrl('produit_service_assistance_entreprise', array('id'=>$service->getId())));
}

public function downloadvideoformation(Service $service)
{
	$em = $this->getDoctrine()->getManager();
	$service->getImgservicesecond()->setNbtele($service->getImgservicesecond()->getNbtele() + 1);
	$em->flush();
	$nameoffile = '/../../../'.$service->getImgservicesecond()->getWebPath();
	return $this->redirect($nameoffile);
}

public function updaterangproduit(Produitformation $produitformation)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['rangproduit']))
	{
		$produitformation->setRang($_POST['rangproduit']);
		$em->flush();
	}
	return $this->redirect($this->generateUrl('users_adminuser_add_un_evenement', 
	array('id'=>$produitformation->getService()->getId())));
}
}