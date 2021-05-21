<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2016
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Produit\Service\MessageType;
use App\Entity\Produit\Service\Message;
use App\Entity\Users\User\Newsletter;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Produit\Produit\Categorie;

class MessageController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function contactus(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$mess = new Message();
    $form = $this->createForm(MessageType::class, $mess);
	if ($request->getMethod() == 'POST'){
	$form->handleRequest($request);
	if($this->getUser() != null)
	{
		$mess->setUser($this->getUser());
	}else{
		if($mess->getNom() == null and $mess->getEmail() == null)
		{
			$this->get('session')->getFlashBag()->add('information','Vous devez entrer votre nom et votre email!');
			return $this->redirect($this->generateUrl('produit_service_contact_us'));
		}
	}
	
    if ($form->isValid()){
		if(isset($_POST['pays']))
		{
			$mess->setPays($_POST['pays']);
		}
		$em->persist($mess);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Votre message a été enregistré avec succès');
		
		//envoie d'email
		$siteweb = $this->params->get('siteweb');
		$sitename = $this->params->get('sitename');
		$emailadmin = $this->params->get('emailadmin');
		if($service->email($emailadmin))
		{

			$response = $this->_servicemail->sendNotifEmail(
				'Team '.$sitename, //Nom du destinataire
				$emailadmin, //Email Destinataire
				$mess->getNom().' viens d\'envoyer un message sur '.$sitename, //Objet de l'email
				$mess->getNom().' viens d\'envoyer un message sur '.$sitename, //Grand Titre de l'email
				$mess->getContenu(),  //Contenu de l'email
				 ''  //Lien à suivre
			);
		}
		
		$oldemail = $em->getRepository(Newsletter::class)
                       ->findBy(array('email'=>$mess->getEmail()));
		if($oldemail == null)
		{
			$newsletter = new Newsletter();
			$newsletter->setNom($mess->getNom());
			$newsletter->setEmail($mess->getEmail());
			$em->persist($newsletter);
			$em->flush();
		}
	}else{
		$this->get('session')->getFlashBag()->add('information','Une erreur a été rencontrée, Vérifier le formulaire !');
	}
	}
	
	$liste_categorie = $em->getRepository(Categorie::class)
	                    ->findAll();
	return $this->render('Theme/Produit/Service/Message/contactus.html.twig',
	 array('form'=>$form->createView(),'liste_categorie'=>$liste_categorie));
}

public function messagerecu()
{
	$em = $this->getDoctrine()->getManager();
	$liste_mess = $em->getRepository(Message::class)
	                 ->myfindAll();
	$formsupp = $this->createFormBuilder()->getForm();
	return $this->render('Theme/Produit/Service/Message/messagerecu.html.twig',
	 array('liste_mess'=>$liste_mess,'formsupp'=>$formsupp->createView()));
}

public function supprimermessage(Message $message, Request $request)
{
	$em = $this->getDoctrine()->getManager();
		$formsupp = $this->createFormBuilder()->getForm();

		if ($request->getMethod() == 'POST') {
		$formsupp->bind($request);
			if ($formsupp->isValid()){
			$em->remove($message);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
			}
		}else{
		$this->get('session')->getFlashBag()->add('supprime_mess',$message->getId());
	    $this->get('session')->getFlashBag()->add('supprime_mess',$message->getTitre());
		}
	return $this->redirect($this->generateUrl('users_adminuser_liste_message_recu'));
}

}