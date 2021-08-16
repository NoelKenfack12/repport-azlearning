<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
*/
namespace App\Controller\Produit\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\User;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Supportchapitre;
use App\Entity\Produit\Produit\Pratiquechapitre;
use App\Entity\Produit\Produit\Exercicepartie;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Form\Produit\Produit\ChapitrecoursType;
use App\Form\Produit\Produit\PartiecoursType;
use App\Form\Produit\Produit\SupportchapitreType;
use App\Form\Produit\Produit\ExercicepartieType;
use App\Form\Produit\Produit\PratiquechapitreType;

use App\Entity\Produit\Produit\Panier;
use App\Entity\Produit\Produit\Produitpanier;

use App\Entity\Produit\Produit\Questionnaire;
use App\Form\Produit\Produit\QuestionnaireType;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;

use App\Entity\Produit\Service\Produitformation;
use App\Entity\Produit\Service\Commentaireblog;

use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;

class ChapitrecoursController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function addnewchapter(Partiecours $partie, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = new Chapitrecours();
	$formchap = $this->createForm(ChapitrecoursType::class, $chapitre); 

	if($request->getMethod() == 'POST' and $this->getUser() != null and isset($_POST['radios1'])){
    $formchap->handleRequest($request);
	if($formchap->isValid()){
		
		if(isset($_POST['dureeminute']))
		{
			$chapitre->setDureeminute($_POST['dureeminute']);
		}
		if(isset($_POST['dureeseconde']))
		{
			$chapitre->setDureeseconde($_POST['dureeseconde']);
		}
		
		$chapitre->setPartiecours($partie);
		if($chapitre->getImgchapitre() != null)
		{
			$chapitre->getImgchapitre()->setServicetext($service);
		}
		if($chapitre->getVideochapitre() != null)
		{
			$chapitre->getVideochapitre()->setServicetext($service);
		}
		if($_POST['radios1'] == 'chapitre')
		{
			$chapitre->setType(0);
		}else if($_POST['radios1'] == 'ressource')
		{
			$chapitre->setType(1);
		}else if($_POST['radios1'] == 'conclusion'){
			$chapitre->setType(2);
		}
		$em->persist($chapitre);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le nouveau chapitre a été enregistré avec succès.');
		
		//envoie d'email
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
	}
	}
	$produit = $partie->getProduit();
	$liste_part = $em->getRepository(Partiecours::class)
	                      ->findBy(array('produit'=>$produit), array('rang'=>'asc'));
	foreach($liste_part as $part)
	{
		$part->setEm($em);
	}
	
	$newpartie = new Partiecours();
	$form = $this->createForm(PartiecoursType::class, $newpartie); 
	
	return $this->render('Theme/Produit/Produit/Chapitrecours/addnewchapter.html.twig', 
	array('formchap'=>$formchap->createView(),'partie'=>$partie,'produit'=>$produit,'liste_part'=>$liste_part,'form'=>$form->createView()));
}

public function presentationchapter(Chapitrecours $chapitre, $mess, GeneralServicetext $service)
{
	$partie = $chapitre->getPartiecours();
	$em = $this->getDoctrine()->getManager();
	$produit = $partie->getProduit();
	$produit->setEm($em);
	
	if($produit->getValide() == true or ($produit->getValide() == false and $produit->getUser() == $this->getUser()) or ($produit->getValide() == false and $this->isGranted('ROLE_GESTION')) or (isset($_GET['codeadmin']) and $_GET['codeadmin'] == 10001))
	{
	if(isset($_GET['codeadmin']))
	{
		$codeadmin = $_GET['codeadmin'];
	}else{
		$codeadmin = 0;
	}
	$liste_part = $em->getRepository(Partiecours::class)
	                      ->findBy(array('produit'=>$produit), array('rang'=>'asc'));
	$ranglecon = 0;
	$trouver = false;
	foreach($liste_part as $part)
	{
		$part->setEm($em);
		$all_chapter = $part->getAllChapitre();
		foreach($all_chapter as $chapter)
		{
			if($trouver == false)
			{
				$ranglecon = $ranglecon + 1;
				if($chapter == $chapitre)
				{
					$trouver = true;
				}
			}else{
				break;
			}
		}
	}

	$bestpanier = null;
	$prodpan = null;
	if($this->getUser() != null)
	{
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	//on cherche à retenir le bon panier .
	foreach($liste_oldpanier as $panier)  //uno    -    le panier lié à un service (une offre de formation) est prio
	{
		$trouve = false;
		foreach($panier->getProduitpaniers() as $propan)
		{
			if($propan->getProduit() == $produit)
			{
				$trouve = true;
				break;
			}
		}
		if($panier->getservice() != null and $trouve == true)
		{
			$bestpanier = $panier;
			break;
		}
	}
	
	if($bestpanier == null)
	{
		foreach($liste_oldpanier as $panier)  // secondo    -    Le panier lié à un produit est bon, s'il ya aucun lié à un service
		{
			$trouve = false;
			foreach($panier->getProduitpaniers() as $propan)
			{
				if($propan->getProduit() == $produit)
				{
					$trouve = true;
					break;
				}
			}
			if($panier->getservice() == null and $panier->getChapitrecours() == null and $trouve == true)
			{
				$bestpanier = $panier;
				break;
			}
		}
	}
	
	if($bestpanier == null)
	{
		foreach($liste_oldpanier as $panier)  // tertio    -    Le panier lié à un chapitre est bon, s'il ya aucun lié à un service ou un produit l'est
		{
			$trouve = false;
			foreach($panier->getProduitpaniers() as $propan)
			{
				if($propan->getProduit() == $produit)
				{
					$trouve = true;
					break;
				}
			}
			if($panier->getservice() == null and $panier->getChapitrecours() == $chapitre and $trouve == true)
			{
				$bestpanier = $panier;
				break;
			}
		}
	}
	}
	
	if($bestpanier != null)
	{
		foreach($bestpanier->getProduitpaniers() as $propan)
		{
			if($propan->getProduit() == $produit)
			{
				$prodpan = $propan;
				break;
			}
		}
	}

	$chapitre->setEm($em);		
	$liste_chapter = $em->getRepository(Chapitrecours::class)
						->listechapitrecours($produit->getId());

	$newpartie = new Partiecours();
	$form = $this->createForm(PartiecoursType::class, $newpartie); 
	
	$session = $this->get('session');

	if($this->getUser() != null)
	{
	$listelikes = $produit->getUserlikes();
	$trouve = false;
	foreach($listelikes as $ser)
	{
		if($this->getUser() == $ser)
		{
			$trouve = true;
			break;
		}
	}
	
	if($trouve == false)
	{
		$produit->addUserlike($this->getUser());
	}
	}else{
		$liste_prod = $session->get('like_produit');
		if($liste_prod != null)
		{
		$tabprod = explode('-',$liste_prod);
		$addlike = true;
		for($i = 0; $i < count($tabprod); $i++)
		{
			if($tabprod[$i] == $produit->getId())
			{
				$addlike = false;
				break;
			}
		}
		
		if($addlike == true)
		{
			$session->set('like_produit',$session->get('like_produit').'-'.$produit->getId());
		}
		
		}else{
			$session->set('like_produit',$produit->getId());
			$addlike = true;
		}
		
		if($addlike == true)
		{
			$produit->setNblike($produit->getNblike() + 1);
		}
	}
	$em->flush();
	
	
	$supportchapitres = new \Doctrine\Common\Collections\ArrayCollection();
	$pratiquechapitres = new \Doctrine\Common\Collections\ArrayCollection();
	$exerciceparties = new \Doctrine\Common\Collections\ArrayCollection();

	foreach($chapitre->getSupportchapitres() as $support)
	{
		$supportchapitres[] = $support;
	}
	foreach($chapitre->getPratiquechapitres() as $pratique)
	{
		$pratiquechapitres[] = $pratique;
	}
	foreach($chapitre->getExerciceparties() as $exercice)
	{
		$exerciceparties[] = $exercice;
	}
	
	$oldservice =  null; //On cherche à obtenir le service approprié auquel ce cours est associé pour affichier la liste des cours.
	if($bestpanier == null)
	{
		$produitformation = $em->getRepository(Produitformation::class)
	                           ->findOneBy(array('produit'=>$chapitre->getPartiecours()->getProduit()), array('date'=>'desc'));
		if($produitformation != null)
		{
			$oldservice = $produitformation->getService();
		}
	}else{
		if($bestpanier->getservice() != null)
		{
			$oldservice = $bestpanier->getservice();
		}else if($panier->getChapitrecours() != null)
		{
			
			$produitformation = $em->getRepository(Produitformation::class)
								   ->findOneBy(array('produit'=>$panier->getChapitrecours()->getPartiecours()->getProduit()), array('date'=>'desc'));
			if($produitformation != null)
			{
				$oldservice = $produitformation->getService();
			}
		}else{
			$produitformation = $em->getRepository(Produitformation::class)
								   ->findOneBy(array('produit'=>$prodpan->getProduit()), array('date'=>'desc'));
			if($produitformation != null)
			{
				$oldservice = $produitformation->getService();
			}
		}
	}
	$cours_formation = new \Doctrine\Common\Collections\ArrayCollection();
	if($oldservice != null)
	{
		$cours_formation = $em->getRepository(Produitformation::class)
	                           ->findBy(array('service'=>$oldservice), array('rang'=>'asc'));
		foreach($cours_formation as $forma)
		{
			$forma->getProduit()->setEm($em);
		}
	}
	
	$messages_cours = new \Doctrine\Common\Collections\ArrayCollection();
	if($this->getUser() != null)
	{
		$messages_cours = $em->getRepository(Commentaireblog::class)
							 ->findBy(array('user'=>$this->getUser(), 'chapitrecours'=>$chapitre), array('date'=>'desc'));
	}
	
	return $this->render('Theme/Produit/Produit/Chapitrecours/presentationchapter.html.twig', 
	array('chapitre'=>$chapitre, 'partie'=>$partie,'prodpan'=>$prodpan,'codeadmin'=>$codeadmin,'bareme'=>$service->getBareme(),
	'liste_chapter'=>$liste_chapter,'produit'=>$produit,'liste_part'=>$liste_part,'form'=>$form->createView(),'supportchapitres'=>$supportchapitres,'oldservice'=>$oldservice,
	'pratiquechapitres'=>$pratiquechapitres,'exerciceparties'=>$exerciceparties,'mess'=>$mess,'cours_formation'=>$cours_formation,
	'messages_cours'=>$messages_cours, 'ranglecon'=>$ranglecon));
	}else{
	
	$this->get('session')->getFlashBag()->add('alertnewsletter','<span class="fa fa-warning"></span> Echec ! vous n\\\'avez pas le droit d\\\'accéder à cette ressource !');

	}
	return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
}

public function modifierchapter(Chapitrecours $chapitre, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$partie = $chapitre->getPartiecours();
	$formchap = $this->createForm(ChapitrecoursType::class, $chapitre); 
	
	$question = new Questionnaire();
	$formquestion = $this->createForm(QuestionnaireType::class, $question);
	if ($request->getMethod() == 'POST' and $this->getUser() != null and isset($_POST['radios1'])){
    $formchap->handleRequest($request);
	if($formchap->isValid()){
		
		if(isset($_POST['dureeminute']))
		{
			$chapitre->setDureeminute($_POST['dureeminute']);
		}
		if(isset($_POST['dureeseconde']))
		{
			$chapitre->setDureeseconde($_POST['dureeseconde']);
		}
		
		$chapitre->setPartiecours($partie);
		
		if($chapitre->getImgchapitre() != null)
		{
			$chapitre->getImgchapitre()->setServicetext($service);
		}
		if($chapitre->getVideochapitre() != null)
		{
			$chapitre->getVideochapitre()->setServicetext($service);
		}
		
		if($_POST['radios1'] == 'chapitre')
		{
			$chapitre->setType(0);
		}else if($_POST['radios1'] == 'ressource')
		{
			$chapitre->setType(1);
		}else if($_POST['radios1'] == 'conclusion'){
			$chapitre->setType(2);
		}
		$em->persist($chapitre);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le chapitre a été modifié avec succès.');
		
		//envoie d'email
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
		}
	}
	$produit = $partie->getProduit();
	$liste_part = $em->getRepository(Partiecours::class)
	                      ->findBy(array('produit'=>$produit), array('rang'=>'asc'));
	foreach($liste_part as $part)
	{
		$part->setEm($em);
	}
	
	$newpartie = new Partiecours();
	$form = $this->createForm(PartiecoursType::class, $newpartie); 
	
	$newsupport = new Supportchapitre();
	$formsupport = $this->createForm(SupportchapitreType::class, $newsupport); 
	
	$pratique = new Pratiquechapitre();
	$formpratique = $this->createForm(PratiquechapitreType::class, $pratique); 
	
	$exercice = new Exercicepartie();
	$formexercice = $this->createForm(ExercicepartieType::class, $exercice); 
	
	return $this->render('Theme/Produit/Produit/Chapitrecours/modifierchapter.html.twig', 
	array('formchap'=>$formchap->createView(),'partie'=>$partie,'produit'=>$produit,'chapitre'=>$chapitre,'liste_part'=>$liste_part,
	'form'=>$form->createView(),'formsupport'=>$formsupport->createView(),'formquestion'=>$formquestion->createView(),'formpratique'=>$formpratique->createView(),'formexercice'=>$formexercice->createView()));
}

public function supprimerchapter(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	if($produit->getUser() == $this->getUser())
	{
		$em->remove($chapitre);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Votre cours a été mis à jour avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec !! Vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_detail_produit_market', array('id'=>$produit->getId())));
}

public function ajoutnouveausupport(Chapitrecours $chapitre, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$newsupport = new Supportchapitre();
	$formsupport = $this->createForm(SupportchapitreType::class, $newsupport); 
	$newsupport->setServicetext($service);

	if ($request->getMethod() == 'POST' and $this->getUser() != null){
    $formsupport->handleRequest($request);
	if($formsupport->isValid()){
		$newsupport->setChapitrecours($chapitre);
		
		$em->persist($newsupport);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le nouveau support a été enregistré avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function telechargersupport(Supportchapitre $support)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $support->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	if($this->getUser() != null)
	{
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	
	foreach($liste_oldpanier as $panier)
	{
		$produitpaniers = $panier->getProduitpaniers();
		foreach($produitpaniers as $propan)
		{
			if(($propan->getProduit() == $produit and $panier->getChapitrecours() == null) or ($propan->getProduit() == $produit and $panier->getChapitrecours() == $chapitre))
			{
				$accesschapter = true;
				break;
			}
		}
		if($accesschapter == true)
		{
			break;
		}
	}
	}
	if($accesschapter == true)
	{
		$nameoffile = '/../../../'.$support->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à ce support de cours.');
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
	}
}

public function supprimersupport(Supportchapitre $support)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $support->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	if($produit->getUser() == $this->getUser())
	{
		$em->remove($support);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Votre cours a été mis à jour avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec !! Vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function ajoutnewtravauxpratique(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	$pratique = new Pratiquechapitre();
	$formpratique = $this->createForm(PratiquechapitreType::class, $pratique); 

	$pratique->setServicetext($service);
	if ($request->getMethod() == 'POST' and $this->getUser() != null){
    $formpratique->handleRequest($request);
	if($pratique->getCorrectionpratique() != null)
	{
		$pratique->getCorrectionpratique()->setServicetext($service);
	}
	if($formpratique->isValid()){
		$pratique->setChapitrecours($chapitre);
		
		$em->persist($pratique);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le nouveau TP a été enregistré avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function formulairemodifpratique(Pratiquechapitre $pratique)
{
	$formpratique = $this->createForm(PratiquechapitreType::class, $pratique); 
	return $this->render('Theme/Produit/Produit/Chapitrecours/formulairemodifpratique.html.twig',
	array('formpratique'=>$formpratique->createView(),'pratique'=>$pratique));
}

public function modifsupportchapter(Pratiquechapitre $pratique, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formpratique = $this->createForm(new PratiquechapitreType(), $pratique); 
	$pratique->setServicetext($service);
	if ($request->getMethod() == 'POST' and $this->getUser() != null){
    $formpratique->handleRequest($request);
	if($pratique->getCorrectionpratique() != null)
	{
		$pratique->getCorrectionpratique()->setServicetext($service);
	}
	if($formpratique->isValid()){
		$em->persist($pratique);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le nouveau TP a été modifié avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$pratique->getChapitrecours()->getId())));
}

public function telechargertp(Pratiquechapitre $tp)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $tp->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	if($this->getUser() != null)
	{
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	
	foreach($liste_oldpanier as $panier)
	{
		$produitpaniers = $panier->getProduitpaniers();
		foreach($produitpaniers as $propan)
		{
			if(($propan->getProduit() == $produit and $panier->getChapitrecours() == null) or ($propan->getProduit() == $produit and $panier->getChapitrecours() == $chapitre))
			{
				$accesschapter = true;
				break;
			}
		}
		if($accesschapter == true)
		{
			break;
		}
	}
	}
	if($accesschapter == true)
	{
		$nameoffile = '/../../../'.$tp->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à ce TP.');
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
	}
}

public function suppressiontpAction(Pratiquechapitre $tp)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $tp->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	if($produit->getUser() == $this->getUser())
	{
		$em->remove($tp);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Votre cours a été mis à jour avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec !! Vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function ajouterexercice(Chapitrecours $chapitre, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$exercice = new Exercicepartie();
	$formexercice = $this->createForm(ExercicepartieType::class, $exercice);  
	$exercice->setServicetext($service);
	if ($request->getMethod() == 'POST' and $this->getUser() != null){
    $formexercice->handleRequest($request);
	if($exercice->getCorrectionexercice() != null)
	{
		$exercice->getCorrectionexercice()->setServicetext($service);
	}
	if($formexercice->isValid()){
		
		$exercice->setChapitrecours($chapitre);
		$em->persist($exercice);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','Le nouveau Exercice a été enregistré avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !!!');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function formulairemodifexercice(Exercicepartie $exercice)
{
	$formexercice1 = $this->createForm(ExercicepartieType::class, $exercice); 
	return $this->render('Theme/Produit/Produit/Chapitrecours/formulairemodifexercice.html.twig',
	array('formexercice1'=>$formexercice1->createView(),'exercice'=>$exercice));
}

public function modifexercicechapter(Exercicepartie $exercice, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formexercice1 = $this->createForm(ExercicepartieType::class, $exercice);  
	$exercice->setServicetext($service);
	if ($request->getMethod() == 'POST' and $this->getUser() != null){
    $formexercice1->handleRequest($request);
	if($exercice->getCorrectionexercice() != null)
	{
		$exercice->getCorrectionexercice()->setServicetext($service);
	}
	if($formexercice1->isValid()){
		$em->persist($exercice);
		$em->flush();

		$this->get('session')->getFlashBag()->add('information','L\'exercice a été modifié avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée !');
	}
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$exercice->getChapitrecours()->getId())));
}

public function downloadcorrectionexo(Exercicepartie $exercice, Produitpanier $prodpan)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $exercice->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	$noteminexo = $this->params->get('noteminexo');
	$chapitre->setEm($em);
	if($this->getUser() != null and $chapitre->getNoteExercice($prodpan) >= $noteminexo)
	{
		$accesschapter = true;
	}else{
		if($this->getUser() == $produit->getUser())
		{
			$accesschapter = true;
		}
	}
	if($accesschapter == true and $exercice->getCorrectionexercice() != null)
	{
		$nameoffile = '/../../../'.$exercice->getCorrectionexercice()->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à cet éxercice.');
		return $this->redirect($this->generateUrl('users_user_detail_panier_user', array('panier'=>$prodpan->getPanier()->getId(), 'produit'=>$produit->getId())));
	}
}

public function telechargcorrectiontp(Pratiquechapitre $pratique, Produitpanier $prodpan)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $pratique->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	$noteminexo = $this->params->get('noteminexo');
	$chapitre->setEm($em);
	if($this->getUser() != null and $chapitre->getNotePratique($prodpan) >= $noteminexo)
	{
		$accesschapter = true;
	}else{
		if($this->getUser() == $produit->getUser())
		{
			$accesschapter = true;
		}
	}
	if($accesschapter == true and $pratique->getCorrectionpratique() != null)
	{
		$nameoffile = '/../../../'.$pratique->getCorrectionpratique()->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à cet éxercice.');
		return $this->redirect($this->generateUrl('users_user_detail_panier_user', array('panier'=>$prodpan->getPanier()->getId(), 'produit'=>$produit->getId())));
	}
}

public function downloadexercice(Exercicepartie $exercice)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $exercice->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	if($this->getUser() != null)
	{
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	
	foreach($liste_oldpanier as $panier)
	{
		$produitpaniers = $panier->getProduitpaniers();
		foreach($produitpaniers as $propan)
		{
			if(($propan->getProduit() == $produit and $panier->getChapitrecours() == null) or ($propan->getProduit() == $produit and $panier->getChapitrecours() == $chapitre))
			{
				$accesschapter = true;
				break;
			}
		}
		if($accesschapter == true)
		{
			break;
		}
	}
	}
	if($accesschapter == true)
	{
		$nameoffile = '/../../../'.$exercice->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à cet éxercice.');
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
	}
}

public function supprimeexercice(Exercicepartie $exercice)
{
	$em = $this->getDoctrine()->getManager();
	$chapitre = $exercice->getChapitrecours();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	if($produit->getUser() == $this->getUser())
	{
		$em->remove($exercice);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Votre cours a été mis à jour avec succès.');
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec !! Vous n\'avez pas le droit d\'effectuer cette action.');
	}
	return $this->redirect($this->generateUrl('produit_produit_user_modif_chapter', array('id'=>$chapitre->getId())));
}

public function ajouterpanier(Chapitrecours $chapitre, GeneralServicetext $service, Request $request)
{
	if(isset($_POST['_password']))
	{
		$em = $this->getDoctrine()->getManager();
		//$nbjour = $this->date->diff(new \Datetime())->days;
		$produit = $chapitre->getPartiecours()->getProduit();
		$liste_chapter = $em->getRepository(Chapitrecours::class)
							->listechapitrecours($produit->getId());
							
		$montant = 	ceil($produit->getNewprise()/count($liste_chapter));	
		
		if($this->getUser()->getSoldeprincipal() >= $montant)
		{
			
			if($_POST['_password'] == $service->decrypt($this->getUser()->getPassword(),$this->getUser()->getSalt()))
			{
				
				$liste_oldpanier = $em->getRepository(Panier::class)
									  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
				$souscription = true;
				
				foreach($liste_oldpanier as $panier)
				{
					$produitpaniers = $panier->getProduitpaniers();
					foreach($produitpaniers as $propan)
					{
						if(($propan->getProduit() == $produit and $panier->getChapitrecours() == null) or ($propan->getProduit() == $produit and $panier->getChapitrecours() == $chapitre))
						{
							$souscription = false;
							break;
						}
					}
					if($souscription == false)
					{
						break;
					}
				}
				
				
				if($souscription == true)
				{
					$this->getUser()->setSoldeprincipal($this->getUser()->getSoldeprincipal() - $montant);
					$liste_oldpanier = $em->getRepository(Panier::class)
										  ->findBy(array('user'=>$this->getUser(),'valide'=>0));
					
					$lastpanier = null;
					foreach($liste_oldpanier as $panier)
					{
						$produitpaniers = $panier->getProduitpaniers();
						foreach($produitpaniers as $propan)
						{
							if(($propan->getProduit() == $produit) and  ($panier->getChapitrecours() == $chapitre))
							{
								$lastpanier = $panier;
								break;
							}
						}
						if($lastpanier != null)
						{
							break;
						}
					}
					
					if($lastpanier == null)
					{
						$panier = new Panier();
						$panier->setUser($this->getUser());
						$panier->setChapitrecours($chapitre);
						$panier->setMontantttc($montant);
						$em->persist($panier);
						
						$produitpanier = new Produitpanier();
						$produitpanier->setPanier($panier);
						$produitpanier->setProduit($produit);
						$produitpanier->setQuantite(1);
						$em->persist($produitpanier);
						$produit->setNbcertificat($produit->getNbcertificat() + 1);
						
						
						//envoie d'email
						$siteweb = $this->params->get('siteweb');
						$sitename = $this->params->get('sitename');
						$emailadmin = $this->params->get('emailadmin');
						
						if($service->email($emailadmin))
						{
							$response = $this->_servicemail->sendNotifEmail(
								'Team '.$sitename, //Nom du destinataire
								$emailadmin, //Email Destinataire
								$this->getUser()->name(30).' vient de s\'inscrire au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de s\'inscrire au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);
						}
						
						if($service->email($produit->getUser()->getUsername()))
						{
							$response = $this->_servicemail->sendNotifEmail(
								$produit->getUser()->name(50), //Nom du destinataire
								$produit->getUser()->getUsername(), //Email Destinataire
								$this->getUser()->name(30).' vient de s\'inscrire au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de s\'inscrire au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);
						}
						
						if($service->email($this->getUser()->getUsername()))
						{

							$response = $this->_servicemail->sendNotifEmail(
								$this->getUser()->name(50), //Nom du destinataire
								$this->getUser()->getUsername(), //Email Destinataire
								'Votre inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' a été effectuée avec succès sur '.$sitename.' !', //Objet de l'email
								'Votre inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' a été effectuée avec succès sur '.$sitename.' !', //Grand Titre de l'email
								'Accédez à votre bableau de bord pour suivre votre progression à cette formation .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);
						}
						
					}else{
						$lastpanier->setDate(new \Datetime());
						$lastpanier->setValide(true);
						$lastpanier->setMontantttc($montant);
						//envoie d'email
						$siteweb = $this->params->get('siteweb');
						$sitename = $this->params->get('sitename');
						$emailadmin = $this->params->get('emailadmin');
						if($service->email($emailadmin))
						{

							$response = $this->_servicemail->sendNotifEmail(
								'Team '.$sitename, //Nom du destinataire
								$emailadmin, //Email Destinataire
								$this->getUser()->name(30).' vient de renouvéler son inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de renouvéler son inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$lastpanier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);

						}
						
						if($service->email($produit->getUser()->getUsername()))
						{

							$response = $this->_servicemail->sendNotifEmail(
								$produit->getUser()->name(50), //Nom du destinataire
								$emailadmin, //Email Destinataire
								$this->getUser()->name(30).' vient de renouvéler son inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Objet de l'email
								$this->getUser()->name(30).' vient de renouvéler son inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' sur '.$sitename, //Grand Titre de l'email
								'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$lastpanier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);

						}
						
						if($service->email($this->getUser()->getUsername()))
						{

							$response = $this->_servicemail->sendNotifEmail(
								$this->getUser()->name(50), //Nom du destinataire
								$this->getUser()->getUsername(), //Email Destinataire
								'Votre inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' a été renouvelée avec succès sur '.$sitename.' !', //Objet de l'email
								'Votre inscription au chapitre '.$chapitre->getTitre().' du cours '.$produit->getTitre().' a été renouvelée avec succès sur '.$sitename.' !', //Grand Titre de l'email
								'Accédez à votre bableau de bord pour suivre votre progression à cette formation .</br><a href="'.$siteweb.'/accueil/user/'.$this->getUser()->getId().'">Lien vers votre tableau de bord.</a>',  //Contenu de l'email
								 ''  //Lien à suivre
							);

						}
					}
					
					$this->get('session')->getFlashBag()->add('information','Inscription au cours effectuée avec succès !');
					$em->flush();
				}else{
					$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous êtes déjà inscrit à une formation contennant ce cours.');
				}
					 
			}else{
				$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Le mot de passe que vous avez entré est invalid. <a href="#!" class="souscription-cours-online" value="0">Reéssayez</a>');
			}
		}else{
			$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Vous n\'avez pas assez de fond pour souscrire à cette formation. <a href=\''.$this->generateUrl("produit_service_yourcv_recrutement").'\'>Créditez votre compte !</a>');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec d\'enregistrement !! Toutes les données n\'ont pas été reçu.');
	}

return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
}

public function downloadvideochapitre(Chapitrecours $chapitre)
{
	$em = $this->getDoctrine()->getManager();
	$partie = $chapitre->getPartiecours();
	$produit = $partie->getProduit();
	$accesschapter = false;
	if($this->getUser() != null)
	{
	$liste_oldpanier = $em->getRepository(Panier::class)
						  ->findBy(array('user'=>$this->getUser(),'valide'=>1));
	
	foreach($liste_oldpanier as $panier)
	{
		$produitpaniers = $panier->getProduitpaniers();
		foreach($produitpaniers as $propan)
		{
			if(($propan->getProduit() == $produit and $panier->getChapitrecours() == null) or ($propan->getProduit() == $produit and $panier->getChapitrecours() == $chapitre))
			{
				$accesschapter = true;
				break;
			}
		}
		if($accesschapter == true)
		{
			break;
		}
	}
	}
	if($accesschapter == true)
	{
		$chapitre->getVideochapitre()->setNbtele($chapitre->getVideochapitre()->getNbtele() + 1);
		$em->flush();
		$nameoffile = '/../../../'.$chapitre->getVideochapitre()->getWebPath();
		return $this->redirect($nameoffile);
	}else{
		$this->get('session')->getFlashBag()->add('information','Echec du téléchargement !! Vous n\'avez pas le droit d\'accéder à cette vidéo.');
		return $this->redirect($this->generateUrl('produit_produit_presentation_chapter', array('id'=>$chapitre->getId())));
	}
}
}