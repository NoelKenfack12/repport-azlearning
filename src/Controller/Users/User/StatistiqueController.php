<?php
/*
    *(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
	*ce fichier est la proprieté de Zentsoft entreprise.
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Produit\Produit\Panier;

class StatistiqueController extends AbstractController
{
    public function etatsouscription($page=1)
    {
        $em = $this->getDoctrine()->getManager();
        if(isset($_POST['annee']) and isset($_POST['mois']) and isset($_POST['jour']))
        {
            $annee = $_POST['annee'];
            $mois = $_POST['mois'];
            $jour = $_POST['jour'];
        }else{
            $annee = date('Y');
            $mois = date('m');
            $jour = 0;
        }

        if($jour == 0)//Sélection de toutes les ventes de la boutique !
		{
			$liste_panier = $em->getRepository(Panier::class)
							   ->findAllCommandeSection($page, 50000,$annee.'-'.$mois.'-01',$annee.'-'.$mois.'-31');
		}else{
			$liste_panier = $em->getRepository(Panier::class)
							   ->findAllCommandeSection($page, 50000,$annee.'-'.$mois.'-'.$jour,$annee.'-'.$mois.'-'.$jour);
		}

        return $this->render('Theme/Users/User/Statistique/etatsouscription.html.twig',
        array('user'=>$this->getUser(), 'currentmois'=>$mois,'currentannee'=>$annee,
        'currentjour'=>$jour, 'liste_panier'=>$liste_panier));
    }
}