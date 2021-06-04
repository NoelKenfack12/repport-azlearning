<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Produit\Produit\PropositionRepository;
use App\Entity\Produit\Produit\Questionnaire;

/**
 * Proposition
 *
 * @ORM\Table("proposition")
 * @ORM\Entity(repositoryClass=PropositionRepository::class)
 */
class Proposition
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
     * @var string
     *
     * @ORM\Column(name="titre", type="text")
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=250, message="Au plus 250 caractès")
     */
    private $titre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="reponse", type="boolean")
     */
    private $reponse;
	
	/**
       * @ORM\ManyToOne(targetEntity=Questionnaire::class, inversedBy="propositions")
       * @ORM\JoinColumn(nullable=false)
    */
	private $questionnaire;
	
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->reponse = false;
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
     * @return Proposition
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
     * Set date
     *
     * @param \DateTime $date
     * @return Proposition
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
		$questionnaire->addProposition($this);

        return $this;
    }

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    /**
     * Set reponse
     *
     * @param boolean $reponse
     * @return Proposition
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;

        return $this;
    }

    /**
     * Get reponse
     *
     * @return boolean 
     */
    public function getReponse()
    {
        return $this->reponse;
    }
}
