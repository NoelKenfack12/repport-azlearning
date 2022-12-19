<?php
/*
	(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Curentwebsite;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Produit\Service\Intervention;
use App\Entity\Users\User\User;
use App\Entity\Users\User\Notification;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Panier;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;
use App\Entity\Produit\Produit\Chapitrecours;
use App\Service\Servicetext\GeneralServicetext;


class CommentaireblogController extends AbstractController
{

private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function postmessageproduit($id, $type, $idmessage, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$siteweb = $this->params->get('siteweb');
	$sitename = $this->params->get('sitename');
	$emailadmin = $this->params->get('emailadmin');
	$emailformateur = '';
	if($type == 'cours')
	{
		$produit = $em->getRepository(Produit::class)
	                  ->find($id);		
		if($produit != null)
		{
			if(isset($_POST['contentmess']))
			{
				$contentmess = htmlspecialchars($_POST['contentmess']);
				$newmessage = new Commentaireblog();
				$newmessage->setProduit($produit);
				$newmessage->setUser($this->getUser());
				$newmessage->setTitre($produit->getTitre());
				$newmessage->setContenu($contentmess);
				$newmessage->setMessage(true);
				$newmessage->setDest($produit->getUser());
				$em->persist($newmessage);
				$em->flush();
			

				if($service->email($emailadmin))
				{
					$response = $this->_servicemail->sendNotifEmail(
						'Team '.$sitename, //Nom du destinataire
						$emailadmin, //Email Destinataire
						$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Objet de l'email
						$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Grand Titre de l'email
						'Veuillez contacter l\'auteur de ce cours pour qu\'il puisse apporter une réponse à ce message, si ce n\'est pas encore le cas.',  //Contenu de l'email
						''  //Lien à suivre
					);
				}
				
				if($service->email($produit->getUser()->getUsername()) == true)
				{
					$emailformateur = $produit->getUser()->getUsername();
				}else if($service->email($produit->getUser()->getMailformateur()) == true){
					$emailformateur = $produit->getUser()->getMailformateur();
				}
			}

			$message = $em->getRepository(Commentaireblog::class)
	                      ->find($idmessage);
			if($message == null)
			{
				$message = $em->getRepository(Commentaireblog::class)
							  ->findOneBy(array('produit'=>$produit, 'user'=>$this->getUser()));
			}
		
			if(strlen($emailformateur) > 3 and $message != null)
			{

				$response = $this->_servicemail->sendNotifEmail(
					$produit->getUser()->name(30), //Nom du destinataire
					$emailadmin, //Email Destinataire
					$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Objet de l'email
					$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Grand Titre de l'email
					'Suivez la progression de cette formation via le lien ci-dessus.</br><a href="'.$siteweb.'/user/detail/user/commande/'.$panier->getId().'/'.$produit->getId().'">Suivez la formation de'.$this->getUser()->name(35).'.</a>',  //Contenu de l'email
					 ''  //Lien à suivre
				);
			}

			$collection1 = new \Doctrine\Common\Collections\ArrayCollection();
			$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
			if($message != null)
			{
				$message->setEm($em);
				if($message->getDest() == $this->getUser())
				{
					$message->setLut(true);
				}

				$liste_reponse = $message->getInterventionsMessage();

				$lastid = 0;
				$compt = 0;
				foreach($liste_reponse as $reponse)
				{
					//$reponse->setLut(true);
					if($compt  == 0)
					{
						$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
						$collection2[]  = $reponse;
						$lastid = $reponse->getUser()->getId();
					}else if($lastid == $reponse->getUser()->getId()){
						$collection2[]  = $reponse;
					}else{
						$collection1[] = $collection2;
						$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
						$collection2[]  = $reponse;
						$lastid = $reponse->getUser()->getId();
					}
					$compt++;
				}
				if(count($collection2) > 0)
				{
					$collection1[] = $collection2;
				}
			}
			$em->flush();
			
			return $this->render('Theme/Produit/Service/Commentaireblog/postmessageproduit.html.twig',
			array('produit'=>$produit, 'message'=>$message, 'collection1'=>$collection1));
		}else{
			echo 0;
			exit;
		}
	}else if($type == 'chapitre')
	{
		$chapitre = $em->getRepository(Chapitrecours::class)
	                  ->find($id);
					  			  
		if($chapitre != null)
		{
			$produit = $chapitre->getPartiecours()->getProduit();
			if(isset($_POST['contentmess']))
			{
				
				$contentmess = htmlspecialchars($_POST['contentmess']);
				$newmessage = new Commentaireblog();
				$newmessage->setChapitrecours($chapitre);
				$newmessage->setUser($this->getUser());
				$newmessage->setTitre($chapitre->getPartiecours()->getProduit()->getTitre());
				$newmessage->setContenu($contentmess);
				$newmessage->setMessage(true);
				$newmessage->setDest($chapitre->getPartiecours()->getProduit()->getUser());
				$em->persist($newmessage);
				$em->flush();

				
				if($service->email($emailadmin))
				{
					$response = $this->_servicemail->sendNotifEmail(
						'Team '.$sitename, //Nom du destinataire
						$emailadmin, //Email Destinataire
						$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Objet de l'email
						$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Grand Titre de l'email
						'Veuillez contacter l\'auteur de ce cours pour qu\'il puisse apporter une réponse à ce message, si ce n\'est pas encore le cas.',  //Contenu de l'email
						 ''  //Lien à suivre
					);

				}
				
				
				if($service->email($produit->getUser()->getUsername()) == true)
				{
					$emailformateur = $produit->getUser()->getUsername();
				}else if($service->email($produit->getUser()->getMailformateur()) == true){
					$emailformateur = $produit->getUser()->getMailformateur();
				}
			}
			
			$message = $em->getRepository(Commentaireblog::class)
	                      ->find($idmessage);
			if($message == null)
			{
				$message = $em->getRepository(Commentaireblog::class)
							  ->findOneBy(array('chapitrecours'=>$chapitre, 'user'=>$this->getUser()));
			}
			
			if(strlen($emailformateur) > 3 and $message != null)
			{
				$response = $this->_servicemail->sendNotifEmail(
					$produit->getUser()->name(30), //Nom du destinataire
					$emailadmin, //Email Destinataire
					$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Objet de l'email
					$this->getUser()->name(30).' vient d\'envoyer un message sur '.$sitename.' à propos du cours '.$produit->getTitre(), //Grand Titre de l'email
					'Veuillez répondre à cette question en cliquant sur le lien ci-dessous.',  //Contenu de l'email
					 ''  //Lien à suivre
				);

			}
			
			$collection1 = new \Doctrine\Common\Collections\ArrayCollection();
			$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
			
			if($message != null)
			{
				$message->setEm($em);
				if($message->getDest() == $this->getUser())
				{
					$message->setLut(true);
				}
				$liste_reponse = $message->getInterventionsMessage();

				$lastid = 0;
				$compt = 0;
				foreach($liste_reponse as $reponse)
				{
					if($compt  == 0)
					{
						$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
						$collection2[]  = $reponse;
						$lastid = $reponse->getUser()->getId();
					}else if($lastid == $reponse->getUser()->getId()){
						$collection2[]  = $reponse;
					}else{
						$collection1[] = $collection2;
						$collection2 = new \Doctrine\Common\Collections\ArrayCollection();
						$collection2[]  = $reponse;
						$lastid = $reponse->getUser()->getId();
					}
					$compt++;
				}
				if(count($collection2) > 0)
				{
					$collection1[] = $collection2;
				}
				$em->flush();
			}
			
			return $this->render('Theme/Produit/Service/Commentaireblog/postmessagechapitre.html.twig',
			array('produit'=>$chapitre->getPartiecours()->getProduit(), 'chapitre'=>$chapitre, 'message'=>$message, 'collection1'=>$collection1));
		}else{
			echo 0;
			exit;
		}
	}else{
		echo 0;
		exit;
	}
}

public function sendmessagetostudent(User $user, Panier $panier, Produit $produit, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	$siteweb = $this->params->get('siteweb');
	$sitename = $this->params->get('sitename');
	$emailadmin = $this->params->get('emailadmin');
	$emailformateur = '';
	if(isset($_POST['messageToStudent']))
	{
		$contentmess = htmlspecialchars($_POST['messageToStudent']);
		$newmessage = new Commentaireblog();
		$newmessage->setProduit($produit);
		$newmessage->setUser($this->getUser());
		$newmessage->setTitre('Vous avez reçu un message à produit de votre formation en '.$produit->getTitre());
		$newmessage->setContenu($contentmess);
		$newmessage->setMessage(true);
		$newmessage->setDest($user);
		$em->persist($newmessage);
		$em->flush();

		if($service->email($user->getUsername()))
		{
			$email = $user->getUsername();
		}else{
			$email = $user->getMailformateur();
		}

		if($service->email($email))
		{
			$response = $this->_servicemail->sendNotifEmail(
				$user->name(50), //Nom du destinataire
				$email, //Email Destinataire
				'Vous avez reçu un message à produit de votre formation en '.$produit->getTitre(), //Objet de l'email
				'Vous avez reçu un message à produit de votre formation en '.$produit->getTitre(), //Grand Titre de l'email
				$contentmess,  //Contenu de l'email
				$siteweb.'/detail/cours/'.$produit->getId().'/'.$newmessage->getId()  //Lien à suivre
			);
		}
	}

	return $this->redirect($this->generateUrl('users_user_detail_panier_user', array('panier'=>$panier->getId(), 'produit'=>$produit->getId())));
}

public function addnewreponse(Commentaireblog $commentaireblog, $type, GeneralServicetext $service)
{
	$em = $this->getDoctrine()->getManager();
	
	$siteweb = $this->params->get('siteweb');
	$sitename = $this->params->get('sitename');
	$emailadmin = $this->params->get('emailadmin');
	
	if($type == 'cours')
	{
		if(isset($_POST['contentmess']))
		{
			$contentmess = htmlspecialchars($_POST['contentmess']);
			$newintervention = new Intervention();
			$newintervention->setCommentaireblog($commentaireblog);
			$newintervention->setUser($this->getUser());
			$newintervention->setContenu($contentmess);
			$newintervention->setMessage(true);
			$em->persist($newintervention);
			$em->flush();
			
			if($commentaireblog->getUser() != $this->getUser())
			{
				$notif = new Notification();
				$notif->setTitre($this->getUser()->name(30).' a répondu à votre message.');
				$notif->setContenu('Formation concernée :'.$commentaireblog->getProduit()->getTitre().'</br>Cliquez sur le lien ci-dessous pour lire ce message.');
				$notif->setUser($commentaireblog->getUser());
				$notif->setCommentaireblog($commentaireblog);
				$em->persist($notif);
				$em->flush();
			}			

			if($this->getUser() == $commentaireblog->getProduit()->getUser() and $this->getUser() != $commentaireblog->getUser())
			{
				$emailuser = '';
				if($service->email($commentaireblog->getUser()->getUsername()) == true)
				{
					$emailuser = $commentaireblog->getUser()->getUsername();
				}else if($service->email($commentaireblog->getUser()->getMailformateur()) == true){
					$emailuser = $commentaireblog->getUser()->getMailformateur();
				}
				
				$emailformateur = '';
				if($service->email($this->getUser()->getUsername()) == true)
				{
					$emailformateur = $this->getUser()->getUsername();
				}else if($service->email($this->getUser()->getMailformateur()) == true){
					$emailformateur = $this->getUser()->getMailformateur();
				}
			
				if($service->email($emailuser) and strlen($emailuser) > 2)
				{

					$response = $this->_servicemail->sendNotifEmail(
						$commentaireblog->getUser()->name(40), //Nom du destinataire
						$emailuser, //Email Destinataire
						$this->getUser()->name(30).' a répondu à votre message.', //Objet de l'email
						$this->getUser()->name(30).' a répondu à votre message.', //Grand Titre de l'email
						'Formation concernée : '.$commentaireblog->getProduit()->getTitre().'</br>Cliquez sur le lien ci-dessous pour lire ce message.',  //Contenu de l'email
						 ''  //Lien à suivre
					);

				}
			}
		}
		return $this->redirect($this->generateUrl('produit_service_post_message_produit_formateur', 
		array('id'=>$commentaireblog->getProduit()->getId(), 'type'=>'cours', 'idmessage'=>$commentaireblog->getId())));
	}else if($type == 'chapitre')
	{
		if(isset($_POST['contentmess']))
		{
			$chapitre = $commentaireblog->getChapitrecours();
			$produit = $chapitre->getPartiecours()->getProduit();
			
			$contentmess = htmlspecialchars($_POST['contentmess']);
			$newintervention = new Intervention();
			$newintervention->setCommentaireblog($commentaireblog);
			$newintervention->setUser($this->getUser());
			$newintervention->setContenu($contentmess);
			$newintervention->setMessage(true);
			$em->persist($newintervention);
			$em->flush();

			if($commentaireblog->getUser() != $this->getUser())
			{
				$notif = new Notification();
				$notif->setTitre($this->getUser()->name(30).' a répondu à votre message.');
				$notif->setContenu('Leçon concernée :'.$chapitre->getTitre().'</br>Cliquez sur le lien ci-dessous pour lire ce message.');
				$notif->setUser($commentaireblog->getUser());
				$notif->setCommentaireblog($commentaireblog);
				$em->persist($notif);
				$em->flush();
			}
			
			if($this->getUser() == $produit->getUser() and $this->getUser() != $commentaireblog->getUser())
			{
				$emailuser = '';
				if($service->email($commentaireblog->getUser()->getUsername()) == true)
				{
					$emailuser = $commentaireblog->getUser()->getUsername();
				}else if($service->email($commentaireblog->getUser()->getMailformateur()) == true){
					$emailuser = $commentaireblog->getUser()->getMailformateur();
				}
				
				$emailformateur = '';
				if($service->email($this->getUser()->getUsername()) == true)
				{
					$emailformateur = $this->getUser()->getUsername();
				}else if($service->email($this->getUser()->getMailformateur()) == true){
					$emailformateur = $this->getUser()->getMailformateur();
				}
			
				if($service->email($emailuser) and strlen($emailuser) > 2)
				{

					$response = $this->_servicemail->sendNotifEmail(
						$commentaireblog->getUser()->name(40), //Nom du destinataire
						$emailuser, //Email Destinataire
						$this->getUser()->name(30).' a répondu à votre message.', //Objet de l'email
						$this->getUser()->name(30).' a répondu à votre message.', //Grand Titre de l'email
						'Leçon concernée : '.$chapitre->getTitre().'</br>Cliquez sur le lien ci-dessous pour lire ce message.',  //Contenu de l'email
						 ''  //Lien à suivre
					);
				}
			}
		}
		return $this->redirect($this->generateUrl('produit_service_post_message_produit_formateur', 
		array('id'=>$commentaireblog->getChapitrecours()->getId(), 'type'=>'chapitre', 'idmessage'=>$commentaireblog->getId())));
	}else{
		echo 0;
		exit;
	}
}

public function listemessage(User $user)
{
	$em = $this->getDoctrine()->getManager();
	if($this->getUser() == $user or $this->isGranted('ROLE_GESTION'))
	{
		$liste_message = $em->getRepository(Commentaireblog::class)
						->findBy(array('dest'=>$this->getUser()), array('date'=>'desc'));

		return $this->render('Theme/Produit/Service/Commentaireblog/listemessage.html.twig',
		array('user'=>$user,'liste_message'=>$liste_message));
	}else{
		return $this->redirect($this->generateUrl('users_user_user_accueil', array('id'=>$user->getId())));
	}
}
}