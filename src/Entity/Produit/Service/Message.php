<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Email;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Validator\Validatortext\Telephone;
use App\Repository\Produit\Service\MessageRepository;

/**
 * Message
 *
 * @ORM\Table("message")
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
 
class Message
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
     * @ORM\Column(name="titre", type="string", length=255)
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=150, message="Au plus 150 caractès")
     */
    private $titre;

	/**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255,nullable=true)
     *@Taillemax(valeur=100, message="Au plus 100 caractès")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=500, message="Au plus 300 caractès")
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255,nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255,nullable=true)
     *@Email()
     */
    private $email;
	
	/**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255,nullable=true)
      *@Telephone()
     */
    private $tel;
	
	/**
      * @ORM\ManyToOne(targetEntity="Users\UserBundle\Entity\User")
      * @ORM\JoinColumn(nullable=true)
      */
    private $user;
	
	public function __construct()
	{
		$this->date = new \Datetime();
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
     * @return Message
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
     * Set contenu
     *
     * @param string $contenu
     * @return Message
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string 
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Message
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
     * Set email
     *
     * @param string $email
     * @return Message
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Message
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Message
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Message
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string 
     */
    public function getPays()
    {
        return $this->pays;
    }
}
