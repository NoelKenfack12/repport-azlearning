<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Recrutement;
use App\Form\Produit\Service\RecrutementType;
use App\Form\Produit\Service\RecrutementeditType;
use App\Entity\Users\User\Newsletter;
use App\Entity\Users\User\Notification;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use General\Service\AfMail\Afmail;
use General\Service\AfMail\fileAttachment;

use App\Service\Afpdf\PDF;
use App\Entity\Produit\Produit\Categorie;
use App\Entity\Produit\Service\Infoentreprise;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Users\User\User;

class RecrutementController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function votredossier(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$recrutement = new Recrutement($service);
	$form = $this->createForm(RecrutementType::class, $recrutement);
	
	if($request->getMethod() == 'POST' and $this->getUser() != null and isset($_POST['moyentransfert']) and isset($_POST['montantransfert']) and isset($_POST['pays']))
	{
		$form->handleRequest($request);
		$liste_banque = $this->getArrayBanque();
		$banquecheck = null;
		foreach($liste_banque as $banque)
		{
			if($banque[0] == $_POST['moyentransfert'])
			{
				$banquecheck = $banque;
				break;
			}
		}
			
		if($form->isValid()  and $_POST['moyentransfert'] != '' and $_POST['montantransfert'] != ''  and $_POST['pays'] != '' and $banquecheck != null and count($banquecheck) > 2){
			$recrutement->setUser($this->getUser());

			$recrutement->setMoyentransfert($banquecheck[1]);
			$recrutement->setMontantransfert($_POST['montantransfert']);
			$recrutement->setPays($_POST['pays']);
			
			$em->persist($recrutement);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','FICHE DE DEMANDE D\'AJOUT DES FONDS A ETE CREEE AVEC SUCCES.');
			
			$oldemail = $em->getRepository(Newsletter::class)
                    ->findBy(array('email'=>$recrutement->getMail()));
			if($oldemail == null and $recrutement->getMail() != null)
			{
				$newsletter = new Newsletter();
				$newsletter->SetNom($recrutement->getUsername());
				$newsletter->SetEmail($recrutement->getMail());
				$em->persist($newsletter);
				$em->flush();
			}
			
			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$conditionuserlink = $this->params->get('conditionuserlink');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');
			
			$tel = $this->params->get('tel');
			$bp = $this->params->get('bp');
				
			// Instanciation de la classe d??riv??e
			$pdf = new PDF('L','mm','A5');
			$pdf->setDate($recrutement->dateFacture());
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->SetFont('Times','',12);

			$pdf->contactstruct($bp,$tel);
			$pdf->contenutransfert($recrutement->numFacture(),$service->retireAccent($recrutement->getUsername()),$recrutement->getTel().' / '.$recrutement->getMail(),' - ',$banquecheck[1],$banquecheck[2],$banquecheck[3],$_POST['montantransfert'].'FCFA');
			$pdf->completeBorder('Az Corporation',$service->retireAccent($recrutement->getUsername()));
			$pdf->SetAuthor('Noel Kenfack');
			
			if(!file_exists ($recrutement->getUploadDossierRootDir()))
			{
				mkdir($recrutement->getUploadDossierRootDir(),0777,true);
			}
			if(!file_exists ($recrutement->getUploadDossierRootDir().'/'.$recrutement->numFacture().'.pdf'))
			{
			$pdf->Output('F',$recrutement->getDossierWebPath());
			}
			
			//Email Administration
			if($service->email($emailadmin))
			{
				$response = $this->_servicemail->sendNotifEmail(
					'Team '.$sitename, //Nom du destinataire
					$emailadmin, //Email Destinataire
					'UNE FICHE DE DEMANDE D\'AJOUT DES FONDS A ETE GENERE SUR '.$sitename.', Contactez le client !', //Objet de l'email
					'UNE FICHE DE DEMANDE D\'AJOUT DES FONDS A ETE GENERE SUR '.$sitename.', Contactez le client !', //Grand Titre de l'email
					'Contacter le client. Retrouvez ses contacts sur la fiche d\'ajout des fonts ci-joins.',  //Contenu de l'email
					 ''  //Lien ?? suivre
				);
			}
				
			//Email Administration
			if($service->email($recrutement->getMail()))
			{

				$response = $this->_servicemail->sendNotifEmail(
					'Team '.$sitename, //Nom du destinataire
					$recrutement->getMail(), //Email Destinataire
					'Vous avez g??n??r?? avec succ??s une fiche de demande d\'ajout de vos fonds sur '.$sitename.' !', //Objet de l'email
					'Vous avez g??n??r?? avec succ??s une fiche de demande d\'ajout de vos fonds sur '.$sitename.' !', //Grand Titre de l'email
					'Retrouvez ci-joint votre FICHE DE DEMANDE D\'AJOUT DES FONDS, Favorisez la prise en charge imm??diate de votre demande en transf??rant ?? temps les frais mentionn??s sur votre fiche.',  //Contenu de l'email
					 ''  //Lien ?? suivre
				);

			}

			return $this->redirect($this->generateUrl('produit_service_dossier_derecrutement_user', array('id'=>$recrutement->getId())));
		}else{
			if($_POST['moyentransfert'] == '' or $_POST['montantransfert'] == '')
			{
				$this->get('session')->getFlashBag()->add('information','Le moyen de tranfert d\'argent ou le montant ?? transf??r?? n\'a pas ??t?? indiqu?? !');
			}else if($_POST['pays'] == '')
			{
				$this->get('session')->getFlashBag()->add('information','Aucun Pays n\'a ??t?? choisi !');
			}else{
				$this->get('session')->getFlashBag()->add('information','Une erreur a ??t?? rencontr??e, V??rifiez vos coordonn??es et retransmettez le formulaire.');
			}
		}
	}

	$liste_categorie = $em->getRepository(Categorie::class)
	                    ->findAll();
	$liste_banque = $this->getArrayBanque();
    return $this->render('Theme\Produit\Service\Recrutement\votredossier.html.twig',
	array('form'=>$form->createView(),'liste_banque'=>$liste_banque,'liste_categorie'=>$liste_categorie));
}

public function getArrayBanque()
{
	$orangemoney = $this->params->get('orangemoney');
	$mtnmobile = $this->params->get('mtnmobile');
	return array(
		array('1','Paiement direct ?? notre bureau','Service Comptable','Az Corporation Cameroun'),
		array('2', 'Afriland FirstBank','AZ CORPORATION','IBAN : CM21 10005 00034 04978021001-48'),
		array('3','Paypal','AZ Corporation','gaielbleriot@gmail.com'),
		array('4','Orange Money','Az Corporation',$orangemoney),
		array('5','MTN Mobile','Az Corporation',$mtnmobile), 
		array('6', ' Autre moyen (MoneyGram, Ria, Western Union,...)','Vous serez contact??','Vous serez contact??')
	);
}

public function affichedossieruser(Recrutement $dossier)
{
	$em = $this->getDoctrine()->getManager();
	$liste_categorie = $em->getRepository(Categorie::class)
	                    ->findAll();
	return $this->render('Theme/Produit/Service/Recrutement/affichedossieruser.html.twig',
	array('dossier'=>$dossier,'liste_categorie'=>$liste_categorie));
}

public function ajoutdossieruserAction(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$recrutement = new Recrutement($service);
	$form = $this->createForm(RecrutementType::class, $recrutement);

	if($request->getMethod() == 'POST')
	{
		$form->handleRequest($request);
		$recrutement->getYourcv()->setServicetext($service);
		if($form->isValid() and $this->getUser() != null){
			$recrutement->setUser($this->getUser());
			$em->persist($recrutement);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Votre dossier a ??t?? soumis avec succ??s.');
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a ??t?? rencontr??e, V??rifiez la taille de votre message et de vos fichiers et retransmettez le formulaire.');
		}
	}
	return $this->redirect($this->generateUrl('produit_service_yourcv_recrutement'));
}

public function listedossier()
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	$liste_dossier = $em->getRepository(Recrutement::class)
	                          ->myfindAll();
	return $this->render('Theme/Users/Adminuser/Recrutement/listedossier.html.twig',
	array('liste_dossier'=>$liste_dossier,'formsupp'=>$formsupp->createView()));
}

public function listedocandcandidature()
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	$liste_dossier = $em->getRepository(Recrutement::class)
	                          ->myfindAll();
	return $this->render('Theme/Users/Adminuser/Recrutement/listedocandcandidature.html.twig',
	array('liste_dossier'=>$liste_dossier,'formsupp'=>$formsupp->createView()));
}

public function supprimerdossier(Recrutement $recrut, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 

	if ($request->getMethod() == 'POST') {
    $formsupp->handleRequest($request);
    if ($formsupp->isValid()){
		if(file_exists ($recrut->getUploadDossierRootDir().'/'.$recrut->numFacture().'.pdf'))
		{
			unlink($recrut->getUploadDossierRootDir().'/'.$recrut->numFacture().'.pdf');
		}
		$em->remove($recrut);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectu??e avec succ??s');
		return $this->redirect($this->generateUrl('users_adminuser_liste_dossier_recrutement'));
	}
	}
    $this->get('session')->getFlashBag()->add('supprime_dossier',$recrut->getId());
	$this->get('session')->getFlashBag()->add('supprime_dossier',$recrut->getUser()->name(20));
	return $this->redirect($this->generateUrl('users_adminuser_liste_dossier_recrutement'));
}

public function validerdossier(Recrutement $recrut, Request $request, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm(); 
	
	if ($request->getMethod() == 'POST'){
    $formsupp->handleRequest($request);
		if ($formsupp->isValid()){

			if($recrut->getValide() == false)
			{
				$recrut->setValide(true);
				$recrut->getUser()->setSoldeprincipal($recrut->getUser()->getSoldeprincipal() + $recrut->getMontantransfert());
				
				//envoie d'email
				$siteweb = $this->params->get('siteweb');
				$sitename = $this->params->get('sitename');
				$emailadmin = $this->params->get('emailadmin');
				
				$notif = new Notification();
				$notif->setTitre('Votre compte a ??t?? cr??dit?? avec succ??s.');
				$notif->setContenu('Un montant de '.$recrut->getMontantransfert().'FCFA a ??t?? d??pos?? sur votre compte via '.$recrut->getMoyentransfert().' pour vos futur commandes sur '.$sitename);
				$notif->setUser($recrut->getUser());
				$em->persist($notif);
				$em->flush();

				if($service->email($recrut->getUser()->getUsername()))
				{
					$response = $this->_servicemail->sendNotifEmail(
						$recrut->getUser()->name(40), //Nom du destinataire
						$recrut->getUser()->getUsername(), //Email Destinataire
						'Votre compte a ??t?? cr??dit?? avec succ??s.', //Objet de l'email
						$this->getUser()->name(30).' vient de d??poser un montant de '.$recrut->getMontantransfert().' FCFA sur votre compte '.$sitename, //Grand Titre de l'email
						'Un montant de '.$recrut->getMontantransfert().'FCFA a ??t?? d??pos?? sur votre compte via '.$recrut->getMoyentransfert().' pour vos futur commande sur '.$sitename,  //Contenu de l'email
						''  //Lien ?? suivre
					);
				}
					
				$this->get('session')->getFlashBag()->add('information','Solde mis ?? jour avec succ??s');
				return $this->redirect($this->generateUrl('users_adminuser_liste_dossier_recrutement'));
			}else{
				if(($recrut->getUser()->getSoldeprincipal() - $recrut->getMontantransfert()) >= 0)
				{
					$recrut->setValide(false);
					$recrut->getUser()->setSoldeprincipal($recrut->getUser()->getSoldeprincipal() - $recrut->getMontantransfert());
					$em->flush();
					$this->get('session')->getFlashBag()->add('information','Solde mis ?? jour avec succ??s');
				}else{
					$this->get('session')->getFlashBag()->add('information','Echec, Le compte ne peut ??tre n??gatif');
				}
				return $this->redirect($this->generateUrl('users_adminuser_liste_dossier_recrutement'));				
			}			
		}
	}
    $this->get('session')->getFlashBag()->add('valider_dossier',$recrut->getId());
	$this->get('session')->getFlashBag()->add('valider_dossier',$recrut->getUser()->name(20));
	return $this->redirect($this->generateUrl('users_adminuser_liste_dossier_recrutement'));
}

public function telechargercv(Recrutement $recrut)
{
	$namefile = '/../../../elearning/public/'.$recrut->getYourcv()->getWebPath();
	return $this->redirect($namefile);
}

public function telechargerlettre(Recrutement $recrut)
{
	$namefile = '/../../../elearning/public/'.$recrut->getDossierWebPath();
	return $this->redirect($namefile);
}

public function souscriptionprogramme(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$recrutement = new Recrutement($service);
	$form = $this->createForm(RecrutementeditType::class, $recrutement);

	if($request->getMethod() == 'POST' and $this->getUser() != null)
	{
		$form->handleRequest($request);
		$recrutement->setUser($this->getUser());
		$recrutement->setDossierformateur(true);
			
		if($form->isValid()){

			$em->persist($recrutement);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Votre demande d\'inscription au programme a ??t?? enregistrer avec succ??s');
			
			$oldemail = $em->getRepository(Newsletter::class)
                          ->findBy(array('email'=>$recrutement->getMail()));
			if($oldemail == null and $recrutement->getMail() != null)
			{
				$newsletter = new Newsletter();
				$newsletter->SetNom($recrutement->getUsername());
				$newsletter->SetEmail($recrutement->getMail());
				$em->persist($newsletter);
				$em->flush();
			}
			
			//envoie d'email
			$siteweb = $this->params->get('siteweb');
			$sitename = $this->params->get('sitename');
			$emailadmin = $this->params->get('emailadmin');

			
			//Email Administration
			if($service->email($emailadmin))
			{
				$response = $this->_servicemail->sendNotifEmail(
					'Team '.$sitename, //Nom du destinataire
					$emailadmin, //Email Destinataire
					'Une inscription au programme formateur a ??t?? enregistr?? avec succ??s '.$sitename.', Contactez le client !', //Objet de l'email
					'Une inscription au programme formateur a ??t?? enregistr?? avec succ??s '.$sitename.', Contactez le client !', //Grand Titre de l'email
					'Contactez le client. Retrouvez ses contacts en vous connectant sur votre espace d\'administration.',  //Contenu de l'email
					 ''  //Lien ?? suivre
				);
			}
				
			//Email Administration
			if($service->email($recrutement->getMail()))
			{

				$response = $this->_servicemail->sendNotifEmail(
					'Team '.$sitename, //Nom du destinataire
					$recrutement->getMail(), //Email Destinataire
					'Vous avez enregistre une demande d\'inscription au programme des formateurs AZ via '.$sitename.' !', //Objet de l'email
					'Vous avez enregistre une demande d\'inscription au programme des formateurs AZ via '.$sitename.' !', //Grand Titre de l'email
					'Vous pourrez ??tre contacte en cas validit?? de votre dossier pas le service des ressources Humaines, Merci de votre int??r??t.',  //Contenu de l'email
					 ''  //Lien ?? suivre
				);
			}

			return $this->redirect($this->generateUrl('produit_service_dossier_derecrutement_user', array('id'=>$recrutement->getId())));
		}else{
			$this->get('session')->getFlashBag()->add('information','Une erreur a ??t?? rencontr??e, V??rifiez vos coordonn??es en retransmettez le formulaire.');
		}
	}
	$article_about = $em->getRepository(Infoentreprise::class)
	                    ->findBy(array('type'=>'article-about'), array('rang'=>'desc'));

    return $this->render('Theme/Produit/Service/Recrutement/souscriptionprogramme.html.twig',
	array('form'=>$form->createView(), 'article_about'=>$article_about));
}

public function consultationoperations($id, GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_GET['id']))
	{
		$id = $_GET['id'];
	}else{
		$id = $id;
	}
	$liste_banque = $this->getArrayBanque();
	$user = $em->getRepository(User::class)
			   ->find($id);
	if($user != null)
	{
		$recrutement = new Recrutement($service);
		$form = $this->createForm(RecrutementType::class, $recrutement);
		if ($request->getMethod() == 'POST'){
			$form->handleRequest($request);

			$banquecheck = null;
			foreach($liste_banque as $banque)
			{
				if($banque[0] == $_POST['moyentransfert'])
				{
					$banquecheck = $banque;
					break;
				}
			}

			if ($form->isValid() and $_POST['moyentransfert'] != '' and $_POST['montantransfert'] != ''  and $_POST['pays'] != '' and $banquecheck != null and count($banquecheck) > 2){
				
				$recrutement->setUser($user);

				$recrutement->setMoyentransfert($banquecheck[1]);
				$recrutement->setMontantransfert($_POST['montantransfert']);
				$recrutement->setPays($_POST['pays']);
				
				$em->persist($recrutement);
				$em->flush();
				$this->get('session')->getFlashBag()->add('information','FICHE DE DEMANDE D\'AJOUT DES FONDS A ETE CREEE AVEC SUCCES.');
				
				$recrutement->setValide(true);
				$user->setSoldeprincipal($user->getSoldeprincipal() + $recrutement->getMontantransfert());

				$em->flush();
				$this->get('session')->getFlashBag()->add('information','Modification effectu??e avec succ??s');
			}else{
				$this->get('session')->getFlashBag()->add('information','Une ereur a ??t?? rencontr??e!');
			}
			return $this->redirect($this->generateUrl('users_adminuser_liste_all_user'));

		}

		$liste_categorie = $em->getRepository(Categorie::class)
	                          ->findAll();
		return $this->render('Theme/Users/Adminuser/Recrutement/consultationoperations.html.twig',
		array('form'=>$form->createView(),'user'=>$user, 'liste_banque'=>$liste_banque,'liste_categorie'=>$liste_categorie));
	}else{
		echo 'Echec ! Une erreur a ??t?? rencontr??e.';
		exit;
	}
}
}