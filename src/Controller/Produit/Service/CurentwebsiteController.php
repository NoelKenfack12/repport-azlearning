<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr>
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Service\Curentwebsite;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurentwebsiteController extends AbstractController
{
private $params;

public function __construct(ParameterBagInterface $params)
{
	$this->params = $params;
}

public function curentwebsite()
{
	$curentwebsite = $this->params->get('curentwebsite');
	$website = explode('-',$curentwebsite);
	$key = '';
	for($i = 0; $i < count($website); $i++)
	{
		$key = $key.''.$website[$i];
	}
	$em = $this->getDoctrine()->getManager();
	$curentsite = $em->getRepository(Curentwebsite::class)
						->findOneBy(array('nom'=>$key));
	$request = $this->getRequest();
	if($request->getMethod() == 'POST')
	{
		if(isset($_POST['key_one']) and isset($_POST['key_two']) and isset($_POST['key_three']))
		{
			$essaie = $_POST['key_one'].''.$_POST['key_two'].''.$_POST['key_three'];
			if($essaie == $key and $curentsite == null)
			{
				$instance = new Curentwebsite();
				$instance->setNom($essaie);
				$em->persist($instance);
				$em->flush();
				return $this->redirect($this->generateUrl('users_user_acces_plateforme'));
			}
		}
	}
	return new Response('<h2 style="color: red;">Cette clé d\'identification n\'existe pas.</h2>');
}
}