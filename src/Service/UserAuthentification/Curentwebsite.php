<?php
namespace App\Service\UserAuthentification;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Curentwebsite
{
	protected $em;
	protected $request;
	protected $curentwebsite;
	
	public function __construct(Request $request,EntityManager $em,$curentwebsite)
	{
		$this->em = $em;
		$this->request = $request;
		$this->curentwebsite = $curentwebsite;
	}
	
	protected function displayBeta(Response $reponse)
	{
		$website = explode('-',$this->curentwebsite);
		$key = '';
		for($i = 0; $i < count($website); $i++)
		{
			$key = $key.''.$website[$i];
		}
		$curentsite = $this->em->getRepository('ProduitServiceBundle:Curentwebsite')
	                        ->findOneBy(array('nom'=>$key));
		if($curentsite == null)
		{
			$content = '
			<!DOCTYPE HTML>
			<!--
				Arcana by HTML5 UP
				html5up.net | @n33co
				Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
			-->
			<html>
				<head>
					<title>Une Erreur a été rencontrée</title>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<link rel="stylesheet" href="/Symfony/web/template/css/bootstrap.css" />
					<link rel="shortcut icon" href="/Symfony/web/template/images/arrow.png">
				</head>
				<body>
					<div id="contenair">
					<div class="row">
					<div class="col-md-6 col-md-offset-3">
					<div class="panel panel-default" style="margin-bottom: 15px; margin-top: 100px;">
					<div class="panel-heading">
					<h3 class="panel-white" style="margin-bottom: 0px; margin-top: 0px;">
					<span class="glyphicon glyphicon-stop"></span> Veuillez saisir la clé de ce produit ! 
					</h3>
					</div>
					<div class="panel-body" style="padding-top: 20px; padding-bottom: 20px; font-size: 15px;">
					<div class="row">
					<form action="key/of/product/authentification" method="post" >
					<div class="col-md-3">
					<input type="password" class="form-control" name="key_one" required/>
					</div>
					<div class="col-md-3">
					<input type="password" class="form-control" name="key_two" required/>
					</div>
					<div class="col-md-3">
					<input type="password" class="form-control" name="key_three" required/>
					</div>
					<div class="col-md-3">
					<button class="btn btn-primary btn-block"/>Valider</button>
					</div>
					</form>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>

				</body>
			</html>';
		$reponse->setContent($content);
		}else{
		$reponse = null;
		}
	return $reponse;
	}
	
	
	public function onKernelResponse(FilterResponseEvent $event)
	{
	$response = $this->displayBeta(new Response());
	if($response != null)
	{
	$event->setResponse($response);
	}
	}
}