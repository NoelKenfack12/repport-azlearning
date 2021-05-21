<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Produit\Produit\ComposquestionnaireRepository;
use App\Entity\Produit\Produit\Questionnaire;
use App\Entity\Produit\Produit\Produitpanier;
use App\Entity\Produit\Produitty\Proposition;

/**
 * Composquestionnaire
 *
 * @ORM\Table("composquestionnaire")
 * @ORM\Entity(repositoryClass=ComposquestionnaireRepository::class)
 */
class Composquestionnaire
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="fermer", type="boolean")
     */
    private $fermer;
	
	/**
     * @var bigint
     *
     * @ORM\Column(name="lastvalidation", type="bigint", nullable=true)
     */
    private $lastvalidation;
	
	/**
     * @ORM\ManyToOne(targetEntity=Questionnaire::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $questionnaire;
	
	/**
     * @ORM\ManyToOne(targetEntity=Produitpanier::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $produitpanier;
	
	/**
     * @ORM\ManyToOne(targetEntity=Proposition::class)
     * @ORM\JoinColumn(nullable=true)
    */
	private $proposition;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->fermer = false;
		$this->lastvalidation = 0;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Composquestionnaire
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

    public function setQuestionnaire(Questionnaire $questionnaire): self
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setProduitpanier(Produitpanier $produitpanier): self
    {
        $this->produitpanier = $produitpanier;

        return $this;
    }

    public function getProduitpanier(): ?Produitpanier
    {
        return $this->produitpanier;
    }

    /**
     * Set lastvalidation
     *
     * @param integer $lastvalidation
     * @return Composquestionnaire
     */
    public function setLastvalidation($lastvalidation)
    {
        $this->lastvalidation = $lastvalidation;

        return $this;
    }

    /**
     * Get lastvalidation
     *
     * @return integer 
     */
    public function getLastvalidation()
    {
        return $this->lastvalidation;
    }

    /**
     * Set fermer
     *
     * @param boolean $fermer
     * @return Composquestionnaire
     */
    public function setFermer($fermer)
    {
        $this->fermer = $fermer;

        return $this;
    }

    /**
     * Get fermer
     *
     * @return boolean 
     */
    public function getFermer()
    {
        return $this->fermer;
    }

    public function setProposition(Proposition $proposition = null): self
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function getProposition(): ?Proposition
    {
        return $this->proposition;
    }
}
