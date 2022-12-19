<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Users\User\Newsletter;
use App\Form\Users\User\NewsletterType;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AfMail\Afmail;
use App\Service\AfMail\fileAttachment;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Service\Email\Singleemail;

class NewsletterController extends AbstractController
{
private $params;
private $_servicemail;

public function __construct(ParameterBagInterface $params, Singleemail $servicemail)
{
	$this->params = $params;
	$this->_servicemail = $servicemail;
}

public function createaccount(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$newsletter = new Newsletter();
	$form = $this->createForm(NewsletterType::class, $newsletter);

	if ($request->getMethod() == 'POST'){
    $form->handleRequest($request);
		if ($form->isValid()){
		$em->persist($newsletter);
		$em->flush();
		$session = $this->get('session');
		$session->set('test_newsletter',100);
		$this->get('session')->getFlashBag()->add('alertnewsletter','<span class="fa fa-info-circle"></span> Inscription a la newsletter effectuée avec succès');
		
		//envoie d'email
		$siteweb = $this->params->get('siteweb');
		$sitename = $this->params->get('sitename');
		$emailadmin = $this->params->get('emailadmin');
		if($service->email($emailadmin))
		{
			$response = $this->_servicemail->sendNotifEmail(
				'Hello Team '.$sitename, //Nom du destinataire
				$emailadmin, //Email Destinataire
				$newsletter->getNom().' viens de suscrire pour la newsletter sur '.$sitename, //Objet de l'email
				$newsletter->getNom().' viens de suscrire pour la newsletter sur '.$sitename, //Grand Titre de l'email
				'Envoyé lui un petit message pour lui souhaité la bienvenue !',  //Contenu de l'email
				 ''  //Lien à suivre
			);

		}
							
		}else{
			$this->get('session')->getFlashBag()->add('alertnewsletter','<span class="fa fa-info-circle"></span> Une erreur a été rencontré, Il se peut que votre email est déjà enregistré');
		}
	}
	return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
}
}