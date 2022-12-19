<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2016
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Produit\Service\MessemailType;
use App\Entity\Produit\Service\Messemail;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Users\User\Newsletter;


class MessemailController extends AbstractController
{
private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}
	
public function messageemail(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$messemail = new Messemail();
    $form = $this->createForm(MessemailType::class, $messemail);
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
	$messemail->setUser($this->getUser());
    if ($form->isValid()){

		//envoie d'email
		$siteweb = $this->params->get('siteweb');
		$sitename = $this->params->get('sitename');
		$emailadmin = $this->params->get('emailadmin');
		
		$liste_newsletter = $em->getRepository(Newsletter::class)
		                       ->findAll();
		foreach($liste_newsletter as $news)
		{
			list($compte,$domaine)=split("@",$news->getEmail(),2);
		    if (checkdnsrr($domaine,"MX") or checkdnsrr($domaine,"A")){

				$response = $this->_servicemail->sendNotifEmail(
					$news->getNom(), //Nom du destinataire
					$news->getEmail(), //Email Destinataire
					$messemail->getTitre(), //Objet de l'email
					$messemail->getTitre(), //Grand Titre de l'email
					$messemail->getContenu(),  //Contenu de l'email
					 ''  //Lien à suivre
				);


		    $courantemail = $em->getRepository(Newsletter::class)
		                 ->findOneBy(array('email'=>$news->getEmail()));
		    if($courantemail != null)
		    {
		    	$courantemail->setLastemail(new \Datetime());
		    	$em->flush();
		    }
		    sleep(5);
	        }
		}
		
		$em->persist($messemail);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée');
	}
	}
	$liste_mess = $em->getRepository(Messemail::class)
	                    ->findAll();
	return $this->render('Theme/Users/Adminuser/Messemail/messemail.html.twig',
	array('form'=>$form->createView(),'liste_mess'=>$liste_mess,'formsupp'=>$formsupp->createView()));
}

public function messageidentifieduser(GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	if(isset($_POST['nomdutilisateur']) and isset($_POST['emailutilisateur']) and isset($_POST['titremessage']) and isset($_POST['contenumessage']))
	{
		//envoie d'email
		$siteweb = $this->params->get('siteweb');
		$sitename = $this->params->get('sitename');
		$emailadmin = $this->params->get('emailadmin');
		if($service->email($_POST['emailutilisateur']) and $service->chaineValide($_POST['nomdutilisateur'],3,150) and $service->chaineValide($_POST['titremessage'],3,250) and $service->chaineValide($_POST['contenumessage'],5,2000))
		{
			$messemail = new Messemail();
			$messemail->setTitre($_POST['titremessage']);
			$messemail->setContenu($_POST['contenumessage']);
			$messemail->setCorrespondant($_POST['emailutilisateur']);
			$messemail->setListe(false);
			$messemail->setUser($this->getUser());
			$em->persist($messemail);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
			
			//envoie d'email

			$response = $this->_servicemail->sendNotifEmail(
				$_POST['nomdutilisateur'], //Nom du destinataire
				$_POST['emailutilisateur'], //Email Destinataire
				$messemail->getTitre(), //Objet de l'email
				$messemail->getTitre(), //Grand Titre de l'email
				$messemail->getContenu(),  //Contenu de l'email
				 ''  //Lien à suivre
			);

		}else{
			$this->get('session')->getFlashBag()->add('information','Une érreur a été rencontrée.');
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Toutes les données n\'ont pas été reçu.');
	}
	return $this->redirect($this->generateUrl('users_adminuser_message_email_new_letter'));
}

public function supprimermessemail(Messemail $mess)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		if ($formsupp->isValid()){
		$em->remove($mess);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
		}
	}else{
		$this->get('session')->getFlashBag()->add('supprime_mess',$mess->getId());
		$this->get('session')->getFlashBag()->add('supprime_mess',$mess->getTitre());
	}
	return $this->redirect($this->generateUrl('users_adminuser_message_email_new_letter'));
}
}