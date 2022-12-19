<?php

namespace App\Entity\Pricing\Offre;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Repository\Pricing\Offre\CaracteristiqueproduitRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Pricing\Offre\Produit;
use App\Entity\Pricing\Offre\Caracteristique;
use App\Entity\Users\User\User;

/**
 * Caracteristiqueproduit
 *
 * @ORM\Table("caracteristiqueproduit")
 * @ORM\Entity(repositoryClass=CaracteristiqueproduitRepository::class)
 */
class Caracteristiqueproduit
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
     * @ORM\Column(name="valeur", type="text")
     *@Taillemax(valeur=250, message="Au plus 250 caractès")
     */
    private $valeur;

    /**
     * @var integer
     *
     * @ORM\Column(name="isChecked", type="boolean")
     */
    private $isChecked;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
	 * @ORM\ManyToOne(targetEntity=Offre::class, inversedBy="caracteristiqueproduits")
	 * @ORM\JoinColumn(nullable=true)
	*/
    private $offre;

    /**
	 * @ORM\ManyToOne(targetEntity=Caracteristique::class, inversedBy="caracteristiqueproduits")
	 * @ORM\JoinColumn(nullable=true)
	*/
    private $caracteristique;

    /**
      * @ORM\ManyToOne(targetEntity=User::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $user;
    
    public function __construct()
	{
		$this->date = new \Datetime();
        $this->isChecked = false;
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
     * Set valeur
     *
     * @param string $valeur
     * @return Caracteristiqueplan
     */
    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * Get valeur
     *
     * @return string 
     */
    public function getValeur()
    {
        return $this->valeur;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Caracteristiqueplan
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

    /**
     * Set caracteristique
     * @return Caracteristiqueplan
     */
    public function setCaracteristique(Caracteristique $caracteristique = null): self
    {
        $this->caracteristique = $caracteristique;
        if($caracteristique != null)
        {
            $caracteristique->addCaracteristiqueproduit($this);
        }
        
        return $this;
    }

    /**
     * Get caracteristique
     */
    public function getCaracteristique(): ?Caracteristique
    {
        return $this->caracteristique;
    }

    /**
     * Set user
     * @return Caracteristiqueplan
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set offre
     * @return Caracteristiqueproduit
     */
    public function setOffre(offre $offre = null): self
    {
        $this->offre = $offre;
		if($offre != null)
		{
			$offre->addCaracteristiqueproduit($this);
		}

        return $this;
    }

    /**
     * Get offre 
     */
    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    /**
     * Set isChecked
     *
     * @param boolean $isChecked
     * @return Offre
     */
    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;

        return $this;
    }

    /**
     * Get isChecked
     *
     * @return boolean 
     */
    public function getIsChecked()
    {
        return $this->isChecked;
    }
}
