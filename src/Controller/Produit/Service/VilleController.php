<?php
/*(c) Noel Kenfack <noel.kenfack@yahoo.fr> Avril 2016
*/
namespace App\Controller\Produit\Service;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Produit\Service\VilleType;
use App\Entity\Produit\Service\Ville;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\Request;

class VilleController extends AbstractController
{
public function ajoutville(GeneralServicetext $service, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$ville = new Ville($service);
    $form = $this->createForm(VilleType::class, $ville);
	$formsupp = $this->createFormBuilder()->getForm();

	if ($request->getMethod() == 'POST' and isset($_POST['pays'])){
	$form->handleRequest($request);
	$ville->setUser($this->getUser());
    if ($form->isValid() and $_POST['pays'] != ''){
		$ville->setPays($_POST['pays']);
		$em->persist($ville);
		$em->flush();
		$this->get('session')->getFlashBag()->add('information','Enregistrement effectué avec succès');
	}else{
		$this->get('session')->getFlashBag()->add('information','Une ereur a été rencontrée, Choisissez une ville et retransmettez le formulaire!');
	}
	}
	$liste_ville = $em->getRepository(Ville::class)
	                    ->findAll();
	return $this->render('Theme/Users/Adminuser/Service/ville.html.twig',
	array('form'=>$form->createView(),'liste_ville'=>$liste_ville,'formsupp'=>$formsupp->createView()));
}

public function supprimerville(Ville $ville, Request $request)
{
	$em = $this->getDoctrine()->getManager();
	$formsupp = $this->createFormBuilder()->getForm();
	
	if ($request->getMethod() == 'POST') {
		$formsupp->handleRequest($request);
		$liste_panier = $em->getRepository(Panier::class)
							->findBy(array('ville'=>$ville));
		if ($formsupp->isValid() and count($ville->getCoutlivraisons()) == 0 and count($liste_panier) == 0){
			$em->remove($ville);
			$em->flush();
			$this->get('session')->getFlashBag()->add('information','Suppression effectuée avec succès');
		}else{
			$this->get('session')->getFlashBag()->add('information','Action réfusée! Il ya les produits dont la livraison est liée à cette ville');
		}
	}else{
		$this->get('session')->getFlashBag()->add('supprime_ville',$ville->getId());
		$this->get('session')->getFlashBag()->add('supprime_ville',$ville->getNom());
	}
	return $this->redirect($this->generateUrl('produit_service_ajouter_ville'));
}
}
?>