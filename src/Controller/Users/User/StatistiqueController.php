<?php
/*
    *(c) Noel Kenfack <noel.kenfack@yahoo.fr> Février 2015
	*ce fichier est la proprieté de Zentsoft entreprise.
*/
namespace App\Controller\Users\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StatistiqueController extends AbstractController
{
    public function etatsouscription()
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
        return $this->render('Theme/Users/User/Statistique/etatsouscription.html.twig',
        array('user'=>$this->getUser(), 'currentmois'=>$mois,'currentannee'=>$annee,'currentjour'=>$jour));
    }
}