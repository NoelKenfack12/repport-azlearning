
<?php
require('fpdf.php');

class PDF extends FPDF
{
// En-tête
function Header()
{
    // Logo
    $this->Image('logoafex.png',6,6,40);
    // Police Arial gras 15
    $this->SetFont('Arial','',9);
	
	$this->SetY(21);
	$this->SetX(130);
	$this->Cell(60,10,'DOIT: Zentsoft Developpez',0,1);
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
	$this->SetY(115);
	$this->SetX(10);
	$this->Cell(60,10,'Noel Kenfack',0,1);
	
	$this->Image('cachet.png',30,115,30);
	
	$this->Image('signature.png',20,120,20);
	
	$this->SetY(120);
	$this->SetX(30);
	$this->Cell(60,10,'.....................',0,1);
	
	$this->SetY(125);
	$this->SetX(40);
	$this->Cell(60,10,'Payer',0,1);
	
	$this->SetFont('Arial','',8);
	$this->SetY(-40);
	$this->SetX(70);
	$this->Cell(60,10,'NOS SERVICES ET PRODUITS',0,1);
	$this->SetY(-35);
	$this->SetX(70);
	$this->Cell(60,10,'Afex Market: E-commerce, www.market.africexplorer.com',0,1);
	$this->SetY(-30);
	$this->SetX(70);
	$this->Cell(60,10,'B Innow: Projets Innovant et Crowd-founding, www.bin.africexplorer.com',0,1);
	$this->SetY(-25);
	$this->SetX(70);
	$this->Cell(60,10,'Africexplorer: Localisation d\'entreprises en Afrique, www.africexplorer.com',0,1);
	$this->SetY(-20);
	$this->SetX(70);
	$this->Cell(60,10,'Afex Hosting: Hébergement web, Sites web, web Marketing, www.hosting.africexplorer.com',0,1);

	$this->SetFont('Arial','I',8);
	$this->SetY(-10);
	$this->SetX(10);
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0);
	
	$this->SetY(-10);
	$this->SetX(120);
	$this->Cell(60,10,'SERVICES APRES VENTE, suivant nos conditions d\'utilisation...',0,1);
	
}

function addProduct($reference, $designation, $quantite, $puht,$mht, $y)
{
	$this->SetFont('Arial','',10);
	$this->SetY($y);
	$this->SetX(55);
	$this->Cell(60,10,$reference,0,1);
	
	$this->SetY($y);
	$this->SetX(90);
	$this->Cell(60,10,$designation,0,1);
	
	$this->SetY($y);
	$this->SetX(140);
	$this->Cell(60,10,$quantite,0,1);
	
	$this->SetY($y);
	$this->SetX(160);
	$this->Cell(60,10,$puht,0,1);
	
	$this->SetY($y);
	$this->SetX(180);
	$this->Cell(60,10,$mht,0,1);
}

function completeBorder($num_date,$nom_client)
{
	$this->SetY(16);
	$this->SetX(130);
	$this->Cell(60,10,$num_date,0,1);
	
	$this->SetFont('Arial','B',8);
	$this->SetY(110);
	$this->SetX(175);
	$this->Cell(60,10,'Le client',0,1);
	
	$this->SetY(115);
	$this->SetX(175);
	$this->Cell(60,10,$nom_client,0,1);
}

function statistique($totalqte, $totalremise,$prix, $ville, $quartier, $tel, $netapayer)
{
	//statistique de la commande
	$this->SetFont('Times','B',12);

	
	$this->Image('bgfacture.png',10,40,45);
	
	$this->SetY(90);
	$this->SetX(70);
	$this->Cell(60,10,'Arrêter à la somme de',0,1);
	
	$this->SetY(95);
	$this->SetX(70);
	$this->Cell(60,10,'En toute lettre: ........',0,1);
	
	$this->SetY(90);
	$this->SetX(130);
	$this->Cell(60,10,'Total remise',0,1);
	
	$this->SetY(95);
	$this->SetX(130);
	$this->Cell(60,10,$totalremise,0,1);
	
	$this->SetY(90);
	$this->SetX(180);
	$this->Cell(60,10,'Net à payer',0,1);
	
	$this->SetFont('Times','I',12);
	$this->SetY(95);
	$this->SetX(180);
	$this->Cell(60,10,$prix,0,1);
}

function description()
{
	$this->SetFont('Times','B',12);
	$this->SetY(33);
	$this->SetX(55);
	$this->Cell(60,10,'Produits/Services',0,1);
	
	$this->SetY(33);
	$this->SetX(90);
	$this->Cell(60,10,'Désignation',0,1);
	
	$this->SetY(33);
	$this->SetX(140);
	$this->Cell(60,10,'P H.T',0,1);
	
	$this->SetY(33);
	$this->SetX(160);
	$this->Cell(60,10,'Qté/Durée',0,1);
	
	$this->SetY(33);
	$this->SetX(180);
	$this->Cell(60,10,'Montant H.T',0,1);
	
	$this->SetFont('Times','',12);
}

}

// Instanciation de la classe dérivée
$pdf = new PDF('L','mm','A5');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
	$pdf->description();
	$pdf->completeBorder('Facture N°: FA4562354 du: 06/02/2016','Kenfack');
	//liste des produits
	$pdf->addProduct('IT2180', 'ITEL2180', '100.00', '1 an','550 000,00', 40);
	
	$pdf->statistique('100,00', '0,00','2 000 000,00', 'Livraison : Douala ','Quartier: Toumbel', 'Tel: 256225662','2 000 000');
	
$pdf->Output();
?>