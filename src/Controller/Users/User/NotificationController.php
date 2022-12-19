<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\User;
use App\Entity\Users\User\Notification;

class NotificationController extends AbstractController
{
public function listenotifications(User $user)
{
	$em = $this->getDoctrine()->getManager();
	
	if($this->getUser() == $user or $this->isGranted('ROLE_GESTION'))
	{
		$liste_notification = $em->getRepository(Notification::class)
						         ->findBy(array('user'=>$this->getUser()), array('date'=>'desc'));
		foreach($liste_notification as $notification)
		{
			$notification->setLut(true);
		}
		$em->flush();
		return $this->render('Theme/Users/User/Notification/listenotifications.html.twig',
		array('user'=>$user,'liste_notification'=>$liste_notification));
	}else{
		return $this->redirect($this->generateUrl('users_user_user_accueil', array('id'=>$user->getId())));
	}
}
}