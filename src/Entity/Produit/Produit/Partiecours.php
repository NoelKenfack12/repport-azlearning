<?php

namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Produit\Produit\PartiecoursRepository;
use App\Entity\Produit\Produit\Produit;
use App\Entity\Produit\Produit\Chapitrecours;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Partiecours
 *
 * @ORM\Table("partiecours")
 * @ORM\Entity(repositoryClass=PartiecoursRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"partiecours:read"}},
 *    denormalizationContext={"groups"={"partiecours:write"}}
 * )
*/
class Partiecours
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"partiecours:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Taillemin(valeur=3, message="Au moins 3 caractères")
     * @Taillemax(valeur=70, message="Au plus 70 caractès")
     * @Groups({"partiecours:read", "partiecours:write"})
     */
    private $titre;

    /**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     * @Groups({"partiecours:read", "partiecours:write"})
     */
    private $rang;
	
	/**
       * @ORM\ManyToOne(targetEntity=Produit::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $produit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
    */
    private $date;
	
	private $em;
	
	public function __construct()
	{
		$this->date = new \Datetime();
	}
	
	public function setEm($em)
	{
		$this->em = $em;
	}
	
	public function getEm()
	{
		return $this->em;
	}

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set titre
     *
     * @param string $titre
     * @return Partiecours
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Partiecours
     */
    public function setRang($rang)
    {
        $this->rang = $rang;

        return $this;
    }

    /**
     * Get rang
     *
     * @return integer 
     */
    public function getRang()
    {
        return $this->rang;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Partiecours
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
	
	public function name($tail)
	{
		if(strlen($this->titre) <= $tail)
		{
		    return $this->titre;
		}else{
            $text = wordwrap($this->titre,$tail,'~',true);
            $tab = explode('~',$text);
            $text = $tab[0];
            return $text.'..';
		}
	}
	
	public function getRessources()
	{
		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$this,'type'=>1), array('rang'=>'asc'));
		return $liste_chapitre;
	}
	
	public function getChapitres()
	{
		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$this,'type'=>0), array('rang'=>'asc'));
		return $liste_chapitre;
	}
	
	public function getConclusion()
	{
		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$this,'type'=>2), array('rang'=>'asc'));
		return $liste_chapitre;
	}
	
	public function getAllChapitre()
	{
		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$this), array('rang'=>'asc'));
		return $liste_chapitre;
	}
	
	public function getDureePartie()
	{
		$liste_chapitre = $this->em->getRepository(Chapitrecours::class)
								->findBy(array('partiecours'=>$this), array('rang'=>'asc'));
		$durreemin = 0;
		$durreeseconde = 0;
		$totalseconde = 0;
		foreach($liste_chapitre as $chapitre)
		{
			$durreemin = $durreemin + $chapitre->getDureeminute();
			$durreeseconde = $durreeseconde + $chapitre->getDureeseconde();
		}
		
		$totalseconde = $durreeseconde + ($durreemin * 60);
		$minute = (int)($totalseconde/60);
		$seconde = $totalseconde % 60;
		return $minute.' min '.$seconde.' s ';
	}
}
