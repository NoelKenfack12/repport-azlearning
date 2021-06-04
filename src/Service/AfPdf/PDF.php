<?php
namespace App\Service\Afpdf;
use Fpdf\Fpdf;

class PDF extends Fpdf
{
private $telstruct;
private $logosite;
private $namesite;
private $sitewebsite;
private $mailsite;
private $date;

public function __construct(
	$orientation = 'P',
	$unit = 'mm',
	$size = 'letter'
) {
	parent::__construct( $orientation, $unit, $size );
	// ...
}

public function getTelstruct()
{
	return $this->telstruct;
}

public function setTelstruct($tel)
{
	$this->telstruct = $tel;
}

public function getLogosite()
{
	return $this->logosite;
}
public function setLogosite($logo)
{
	$this->logosite = $logo;
}

public function getNamesite()
{
	return $this->namesite;
}
public function setNamesite($name)
{
	$this->namesite = $name;
}

public function getSitewebsite()
{
	return $this->sitewebsite;
}
public function setSitewebsite($site)
{
	$this->sitewebsite = $site;
}

public function getMailsite()
{
	return $this->mailsite;
}
public function setMailsite($mail)
{
	$this->mailsite = $mail;
}

public function getDate()
{
	return $this->date;
}
public function setDate($date)
{
	$this->date = $date;
}

// En-tête
function Header()
{
    // Logo
    $this->Image(__DIR__.'\logoafex.png',3,6,40);
    // Police Arial gras 15
    $this->SetFont('Arial','',9);
	
	$this->Image(__DIR__.'\ligne2.png',65,38,150);
	
	$this->SetFont('Arial','i',8);
	$this->SetY(0);
	$this->SetX(192);
	$this->Cell(60,10,$this->getDate(),0,1);
	$this->Image(__DIR__.'\ligneright.png',65,4,150);
    // Saut de ligne
    $this->Ln(5);
}

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    // Police Arial italique 8
    $this->SetFont('Arial','B',8);
    // Numéro de page
	$this->SetY(110);
	$this->SetX(10);
	$this->Cell(60,10,'Directeur Commercial et finnacier',0,1);
	
	$this->SetY(120);
	$this->SetX(30);
	$this->Cell(60,10,'.....................',0,1);
	
	$this->SetFont('Arial','B',12);
	$this->SetTextColor(239,39,39);
	$this->SetY(125);
	$this->SetX(15);
	$this->Cell(60,10,'Non payer',0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetTextColor(0,0,0);
	$this->SetFont('Arial','',8);
	$this->SetY(-40);
	$this->SetX(67);
	$this->Cell(60,10,'SERVICES ET PRODUITS',0,1);
	$this->SetY(-35);
	$this->SetX(67);
	$this->Cell(60,10,'Nos Services: www.azcorporation.net/services',0,1);
	$this->SetY(-30);
	$this->SetX(67);
	$this->Cell(60,10,'E-learning: www.e-learning.azcorporation.net',0,1);

	$this->SetFont('Arial','I',8);
	$this->SetY(-10);
	$this->SetX(10);
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0);
	
	$this->SetY(-10);
	$this->SetX(67);
	$this->Cell(50,10,'Contr. No : po81812150016j             RCCM : RC/Y AO/2014/A/4758             compte No : 04319801051',0,1);
	
	$this->Link(100, 140, 120, 10,$this->getSitewebsite().'/questions/reponses');
	$this->Image(__DIR__.'\ligneright.png',65,134,150);
}

function contactstruct($bp,$tel)
{
	$this->SetFont('Arial','B',10);
	$this->SetY(70);
	$this->SetX(10);
	$this->Cell(60,10,'Direction générale',0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY(75);
	$this->SetX(10);
	$this->Cell(60,10,'Ville:',0,1);
	
	$this->SetFont('Arial','',8);
	$this->SetY(75);
	$this->SetX(18);
	$this->Cell(60,10,$bp.' Yaoundé - Bambis',0,1);
	$this->SetFont('Arial','B',8);
	$this->SetY(80);
	$this->SetX(10);
	$this->Cell(60,10,'Site web: ',0,1);
	
	$this->SetFont('Arial','',8);
	$this->SetY(80);
	$this->SetX(24);
	$this->Cell(60,10,'www.azcorporation.net',0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY(85);
	$this->SetX(10);
	$this->Cell(60,10,'E-mail: ',0,1);
	
	$this->SetFont('Arial','',8);
	$this->SetY(85);
	$this->SetX(21);
	$this->Cell(60,10,'infos@azcorporation.net',0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY(90);
	$this->SetX(10);
	$this->Cell(60,10,'Téléphone: ',0,1);
	$this->SetFont('Arial','',8);
	$this->SetY(90);
	$this->SetX(27);
	$this->Cell(60,10,$tel,0,1);
}

function contenutransfert($num,$nom,$mail,$cni,$operateur,$compte,$numcompte,$montant)
{
	$this->SetFont('Arial','',9);

	$this->SetY(42);
	$this->SetX(65);
	$this->Cell(137,10,'',1,1);
	
	$this->SetFont('Arial','B',12);
	$this->SetY(43);
	$this->SetX(67);
	$this->Cell(60,10,'FICHE D\'AJOUT DU CREDIT D\'ACHAT N° '.$num,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(55);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(54);
	$this->SetX(67);
	$this->Cell(60,10,'Nom et Prenom:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(54);
	$this->SetX(100);
	$this->Cell(60,10,$nom,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(62);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(61);
	$this->SetX(67);
	$this->Cell(60,10,'Tel et E-mail:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(61);
	$this->SetX(100);
	$this->Cell(60,10,$mail,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(69);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(68);
	$this->SetX(67);
	$this->Cell(60,10,'CNI N°:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(68);
	$this->SetX(100);
	$this->Cell(60,10,$cni,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(76);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(75);
	$this->SetX(67);
	$this->Cell(60,10,'Opérateur:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(75);
	$this->SetX(100);
	$this->Cell(60,10,$operateur,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(83);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(82);
	$this->SetX(67);
	$this->Cell(60,10,'Compte:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(82);
	$this->SetX(100);
	$this->Cell(60,10,$compte,0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(90);
	$this->SetX(67);
	$this->Cell(60,10,'...........................................................................................................................',0,1);
	
	$this->SetFont('Times','B',12);
	$this->SetY(89);
	$this->SetX(67);
	$this->Cell(60,10,'N° du compte:',0,1);
	
	$this->SetFont('Times','',12);
	$this->SetY(89);
	$this->SetX(100);
	$this->Cell(60,10,$numcompte,0,1);
	
	$this->SetY(100);
	$this->SetX(116);
	$this->Cell(85,10,'',1,1);
	
	$this->SetFont('Arial','B',11);
	$this->SetY(100);
	$this->SetX(116);
	$this->Cell(60,10,'MONTANT À TRANSFERER: '.$montant,0,1);
}


function completeBorder($nomdg,$nom_client)
{
	$this->SetFont('Arial','B',8);
	$this->SetY(115);
	$this->SetX(10);
	$this->Cell(60,10,$nomdg,0,1); 
	
	$this->SetY(110);
	$this->SetX(175);
	$this->Cell(60,10,'Le client',0,1);
	
	$this->SetY(115);
	$this->SetX(175);
	$this->Cell(60,10,$nom_client,0,1);
}

}

?>