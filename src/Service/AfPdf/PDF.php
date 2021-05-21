<?php
/**
    *
    * � Copyright 2016 Afrique Explorer
    */
namespace App\Service\AfPdf;

require_once(dirname(__FILE__) . '/fpdf.php');


class PDF extends FPDF
{
	
// En-t�te
function Header()
{
    
}

// Pied de page
function Footer()
{
    // Positionnement � 1,5 cm du bas
    // Police Arial italique 8
	// $this->SetFont('Arial','I',8);
	// $this->SetY(-10);
	// $this->SetX(10);
    // $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0);
}

public function myheader($num_date,$nom_client,$titrecommande)
{
	// Logo
    $this->Image(__DIR__.'/../../../../web/template/images/facture/logom.png',6,2,40);
    // Police Arial gras 15
    $this->SetFont('Arial','',9);
    $this->SetY(11);
	$this->SetX(10);
    $this->Cell(30,10,'30020 Yaound�, Carrefour Kameni, Biyem-assi',0,1);
	$this->SetY(16);
	$this->SetX(10);
    $this->Cell(60,10,'Commerce G�n�ral. Gros et Semi-Gros, Prestation de service',0,1);
	$this->SetY(21);
	$this->SetX(10);
    $this->Cell(50,10,'T�l: (00237) 693839823 . 680288416 / Internet: www.market.afhunt.com/',0,1);
	
	$this->SetFont('Arial','B',12);
	$this->SetY(11);
	$this->SetX(130);
	$this->Cell(60,10,$titrecommande,0,1);
	
	$this->SetFont('Arial','',9);
	$this->SetY(16);
	$this->SetX(130);
	$this->Cell(60,10,$num_date,0,1);
	
	$this->SetY(21);
	$this->SetX(130);
	$this->Cell(60,10,'DOIT: '.$nom_client,0,1);
	
    // Saut de ligne
    $this->Ln(1);
}

function headerdescription()
{
	$this->SetFont('Times','B',12);
	$this->SetY(33);
	$this->SetX(10);
	$this->Cell(30,10,'R�f�rence',1,1);
	
	$this->SetY(33);
	$this->SetX(40);
	$this->Cell(90,10,'D�signation',1,1);
	
	$this->SetY(33);
	$this->SetX(130);
	$this->Cell(20,10,'Qt�',1,1);
	
	$this->SetY(33);
	$this->SetX(150);
	$this->Cell(20,10,'P.U H.T',1,1);
	
	$this->SetY(33);
	$this->SetX(170);
	$this->Cell(30,10,'Montant H.T',1,1);
	
	$this->SetFont('Times','',12);
}

function headerdescriptionLivreurMobile()
{
	$this->SetFont('Times','B',12);
	$this->SetY(33);
	$this->SetX(10);
	$this->Cell(20,10,'R�f�rence',1,1);
	
	$this->SetY(33);
	$this->SetX(30);
	$this->Cell(80,10,'D�signation',1,1);
	
	$this->SetY(33);
	$this->SetX(110);
	$this->Cell(10,10,'Qt�',1,1);
	
	$this->SetY(33);
	$this->SetX(120);
	$this->Cell(20,10,'P.U H.T',1,1);
	
	$this->SetY(33);
	$this->SetX(140);
	$this->Cell(10,10,'N.P.',1,1);
	
	$this->SetY(33);
	$this->SetX(150);
	$this->Cell(20,10,'D.P.',1,1);
	
	$this->SetY(33);
	$this->SetX(170);
	$this->Cell(30,10,'Montant H.T',1,1);
	
	$this->SetFont('Times','',12);
}


function addProduct($reference, $designation, $quantite, $puht,$mht)
{
	//statistique de la commande
	$this->SetFont('Times','',12);
	$y = $this->GetY();
	$this->SetY($y);
	$this->SetX(10);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(30,6,$reference,1,'L',0);
	
	$this->SetY($y);
	$this->SetX(40);
	$this->SetFillColor(0, 198, 215);
	$this->Cell(90,6,$designation,1,0);
	
	$this->SetY($y);
	$this->SetX(130);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(20,6,$quantite,1,'L',0);
	
	$this->SetY($y);
	$this->SetX(150);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(20,6,$puht,1,'L',0);
	
	$this->SetY($y);
	$this->SetX(170);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(30,6,$mht,1,'L',0);
}

function addProductLivreurMobile($reference, $designation, $quantite, $puht, $nbp, $detailp, $mht)
{
	//statistique de la commande
	
	$this->SetFont('Times','B',12);
	
	$y = $this->GetY();
	$this->SetY($y);
	$this->SetX(10);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(20,6,$reference,1,'L',0);
	
	$this->SetY($y);
	$this->SetX(30);
	$this->SetFillColor(0, 198, 215);
	$this->Cell(80,6,$designation,1,0);
	
	$this->SetY($y);
	$this->SetX(110);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(10,6,$quantite,1,'L',0);
	
	$this->SetY($y);
	$this->SetX(120);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(20,6,$puht,1,'L',0);

	$this->SetY($y);
	$this->SetX(140);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(10,6,$nbp,1,'L',0);

	$this->SetY($y);
	$this->SetX(150);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(20,6,$detailp,1,'L',0);

	$this->SetY($y);
	$this->SetX(170);
	$this->SetFillColor(0, 198, 215);
	$this->MultiCell(30,6,$mht,1,'L',0);
	
	$this->SetFont('Times','',12);
}

function statistique($totalqte, $totalremise,$prix, $ville, $quartier, $tel, $netapayer,$page,$client,$admin,$caisse)
{
	//statistique de la commande
	$this->SetFont('Times','B',12);
	$y = $this->getY();
	$this->SetY($y);
	$this->SetX(10);
	$this->Cell(60,10,'Arr�te Le montant TTC � la sommes de',0,1);
	
	$this->SetY($y+5);
	$this->SetX(10);
	$this->Cell(60,10,'..............',0,1);
	
	$this->SetY($y);
	$this->SetX(90);
	$this->Cell(60,10,'Total Qt�',0,1);
	
	$this->SetY($y+5);
	$this->SetX(90);
	$this->Cell(60,10,$totalqte,0,1);
	
	$this->SetY($y);
	$this->SetX(130);
	$this->Cell(60,10,'Total remise',0,1);
	
	$this->SetY($y+5);
	$this->SetX(130);
	$this->Cell(60,10,$totalremise,0,1);
	
	$this->SetY($y);
	$this->SetX(170);
	$this->Cell(60,10,'Net � payer',0,1);
	
	$this->SetFont('Times','I',12);
	$this->SetY($y+5);
	$this->SetX(170);
	$this->Cell(60,10,$prix,0,1);
	
	
	
	$this->SetFont('Times','I',12);
	$this->SetY($y+10);
	$this->SetX(10);
	$this->Cell(60,10,$ville.', '.$quartier,0,1);
	
	$this->SetY($y+10);
	$this->SetX(130);
	$this->Cell(60,10,$tel,0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY($y+10);
	$this->SetX(170);
	$this->Cell(60,10,$netapayer,0,1);
	
	$this->SetFont('Arial','I',8);
	$this->SetY($y+20);
	$this->SetX(60);
	$this->Cell(60,10,'Les marchandises achet�es ne sont ni reprises, ni �chang�es !',0,1);
	
	$this->SetFont('Arial','I',8);
	$this->SetY($y+25);
	$this->SetX(10);
	$this->Cell(60,10,'Le client',0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY($y+30);
	$this->SetX(10);
	$this->Cell(60,10,$client,0,1);
	
	$this->SetFont('Arial','I',8);
	$this->SetY($y+25);
	$this->SetX(170);
	$this->Cell(60,10,$caisse,0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY($y+30);
	$this->SetX(170);
	$this->Cell(60,10,$admin,0,1);
	
	$this->SetFont('Arial','i',8);
	$this->SetY($y+40);
	$this->SetX(10);
	$this->Cell(80,10,'Page '.$page.'/'.$page,0,1);
	
	$this->SetY($y+40);
	$this->SetX(140);
	$this->Cell(80,10,'Propuls�e par: www.afhunt.com',0,1,'C');
}

public function getPDFHeight($nbline)
{
	$hauteur = 121;
	if($nbline == 1)
	{
		$hauteur = 121;
	}else if($nbline == 2)
	{
		$hauteur = 126;
	}else if($nbline == 3)
	{
		$hauteur = 132;
	}else if($nbline == 4)
	{
		$hauteur = 138;
	}else if($nbline == 5)
	{
		$hauteur = 145;
	}else if($nbline == 6)
	{
		$hauteur = 152;
	}else if($nbline == 7)
	{
		$hauteur = 158;
	}else if($nbline == 8)
	{
		$hauteur = 168;
	}else if($nbline == 9)
	{
		$hauteur = 169;
	}else if($nbline == 10)
	{
		$hauteur = 175;
	}else{
		$hauteur = 175;
	}
}

}

?>