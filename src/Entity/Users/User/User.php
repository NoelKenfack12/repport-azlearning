<?php

namespace App\Entity\Users\User;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Email;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Validator\Validatortext\Password;
use App\Validator\Validatortext\Telephone;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Validatortext\Telormail;
use App\Repository\Users\User\UserRepository;
use App\Entity\Produit\Service\Ville;
use App\Entity\Users\User\Imgprofil;
use App\Entity\Users\User\Imgcouverture;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User
 *
 * @ORM\Table("user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"user:read"}},
 *    denormalizationContext={"groups"={"user:write"}}
 * )
 * @UniqueEntity(fields="username", message="Ce  mail existe déjà.")
 * @UniqueEntity(fields="tel", message="Ce  numéro existe déjà.")
 ** @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Taillemin(valeur=3, message="Au moins 3 caractères")
     * @Taillemax(valeur=70, message="Au plus 70 caractès")
     * @Groups({"user:read", "user:write"})
     */
    private $nom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
    */
    private $prenom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     *@Telormail()
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     *@Password()
     */
    private $password;
	
	/**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    private $salt;
	
	/**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     *@Telephone()
     */
    private $tel;
	
	/**
     * @var string
     *
     * @ORM\Column(name="diplome", type="string", length=255, nullable=true)
     */
    private $diplome;
	
	/**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=255, nullable=true)
     */
    private $poste;
	
	/**
     * @var text
     *
     * @ORM\Column(name="message", type="text", length=255, nullable=true)
     */
    private $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mailval", type="boolean")
     */
    private $mailval;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="telpublic", type="boolean")
     */
    private $telpublic;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="formateur", type="boolean")
     */
    private $formateur;
	
	/**
     * @var string
     *
     * @ORM\Column(name="mailformateur", type="string", length=255, nullable=true)
     */
    private $mailformateur;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbticket", type="integer")
     */
    private $nbticket;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbgaim", type="integer")
     */
    private $nbgaim;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="soldetransit", type="integer")
     */
    private $soldetransit;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="soldeprincipal", type="integer")
     */
    private $soldeprincipal;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="soldegain", type="integer")
     */
    private $soldegain;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbmail", type="integer")
     */
    private $nbmail;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="nbdossier", type="integer")
     */
    private $nbdossier;

    /**
     * @var integer
     *
     * @ORM\Column(name="datebeg", type="bigint")
     */
    private $datebeg;

    /**
     * @var integer
     *
     * @ORM\Column(name="dateend", type="bigint")
     */
    private $dateend;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateins", type="datetime")
     */
    private $dateins;

    /**
     * @var boolean
     *
     * @ORM\Column(name="telval", type="boolean")
     */
    private $telval;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="datenaiss", type="datetime")
     */
    private $datenaiss;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="sexe", type="boolean")
     */
	private $sexe;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;
	
	/**
       * @ORM\ManyToOne(targetEntity=Ville::class)
       * @ORM\JoinColumn(nullable=true)
    */
	private $ville;
	
	/**
         * @ORM\OneToOne(targetEntity=Imgprofil::class, mappedBy="user")
         */
    private $imgprofil;
	
	/**
         * @ORM\OneToOne(targetEntity=Imgcouverture::class, mappedBy="user")
         */
    private $imgcouverture;
	
	private $servicetext;
	
	private $datepicket;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->mailval = false;
		$this->telval = false;
		$this->sexe = false;
		$this->message = '';
		$this->telpublic = false;
		$this->formateur = false;
		$this->dateins = new \Datetime();
		$this->datebeg = time();
		$this->dateend = time();
		$this->nbticket = 0;
		$this->nbgaim = 0;
		$this->soldeprincipal = 0;
		$this->nbmail = 0;
		$this->nbdossier = 0;
		$this->soldegain = 0;
		$this->soldetransit = 0;
		$this->roles = array('ROLE_USER');
	}
	
	public function getServicetext()
	{
		return $this->servicetext;
	}
	
	public function setServicetext(GeneralServicetext $service)
	{
		$this->servicetext = $service;
	}
	
	public function getDatepicket()
	{
		return $datepicket;
	}
	
	public function setDatepicket($datepicket)
	{
		$this->datepicket = $datepicket;
	}

	public function age()
	{
	$nbjour = $this->datenaiss->diff(new \Datetime())->days;
	$age = round($nbjour/365);
	return $age;
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
     * Set nom
     *
     * @param string $nom
     * @return User
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

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return User
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
     * Set datebeg
     *
     * @param integer $datebeg
     * @return User
     */
    public function setDatebeg($datebeg)
    {
        $this->datebeg = $datebeg;

        return $this;
    }

    /**
     * Get datebeg
     *
     * @return integer 
     */
    public function getDatebeg()
    {
        return $this->datebeg;
    }

    /**
     * Set dateend
     *
     * @param integer $dateend
     * @return User
     */
    public function setDateend($dateend)
    {
        $this->dateend = $dateend;

        return $this;
    }

    /**
     * Get dateend
     *
     * @return integer 
     */
    public function getDateend()
    {
        return $this->dateend;
    }

    /**
     * Set dateins
     *
     * @param \DateTime $dateins
     * @return User
     */
    public function setDateins($dateins)
    {
        $this->dateins = $dateins;

        return $this;
    }

    /**
     * Get dateins
     *
     * @return \DateTime 
     */
    public function getDateins()
    {
        return $this->dateins;
    }

    /**
     * Set telval
     *
     * @param boolean $telval
     * @return User
     */
    public function setTelval($telval)
    {
        $this->telval = $telval;

        return $this;
    }

    /**
     * Get telval
     *
     * @return boolean 
     */
    public function getTelval()
    {
        return $this->telval;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return User
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

    /**
     * Set mailval
     *
     * @param boolean $mailval
     * @return User
     */
    public function setMailval($mailval)
    {
        $this->mailval = $mailval;

        return $this;
    }

    /**
     * Get mailval
     *
     * @return boolean 
     */
    public function getMailval()
    {
        return $this->mailval;
    }
	
	/**
     * @ORM\PrePersist()
     */
    public function premajuscule()
	{
	$text1 = $this->servicetext->retireAccent($this->nom);
	$text1 = strtolower($text1);
	$this->nom = ucwords($text1);
	$datenaiss = $this->servicetext->normaliseDate($this->datepicket);
	$this->datenaiss = new \Datetime($datenaiss);
	}
	
	 /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }
	
	public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }
	
	public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }
	
	/**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt()
    {
        return $this->salt;
    }
	
	/**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }
	
	public function eraseCredentials()
	{
	
	}
	public function name($tail)
	{
		$name = $this->prenom .' '.$this->nom;
		if(strlen($name) <= $tail)
		{
			return $name;
		}else{
			$text = wordwrap($name,$tail,'~',true);
			$tab = explode('~',$text);
			$text = $tab[0];
			return $text.'';
		}
	}

    public function setImgprofil(Imgprofil $imgprofil = null): self
    {
        $this->imgprofil = $imgprofil;

        return $this;
    }

    public function getImgprofil(): ?Imgprofil
    {
        return $this->imgprofil;
    }

    /**
     * Set nbmail
     *
     * @param integer $nbmail
     * @return User
     */
    public function setNbmail($nbmail)
    {
        $this->nbmail = $nbmail;

        return $this;
    }

    /**
     * Get nbmail
     *
     * @return integer 
     */
    public function getNbmail()
    {
        return $this->nbmail;
    }
	
	public function mois($nbre)
	{
	$mois ='';
	switch ($nbre)
    {
    case 1: 
    $mois = 'Janvier';
    break;
    case 2: 
	$mois = 'Février';
	break;
	case 3: 
	$mois = 'Mars';
	break;
	case 4: 
	$mois = 'Avril';
	break;
	case 5: 
	$mois = 'Mai';
	break;
	case 6: 
	$mois = 'Juin';
	break;
	case 7: 
	$mois = 'Juillet';
	break;
	case 8: 
	$mois = 'Août';
	break;
	case 9: 
	$mois = 'Septembre';
	break;
	case 10: 
	$mois = 'Octobre';
	break;
	case 11: 
	$mois = 'Novembre';
	break;
	case 12: 
	$mois = 'Decembre';
	break;
	default:
	$mois = 'Janvier';
	break;
	}
	return $mois;
	}

    /**
     * Set nbdossier
     *
     * @param integer $nbdossier
     * @return User
     */
    public function setNbdossier($nbdossier)
    {
        $this->nbdossier = $nbdossier;

        return $this;
    }

    /**
     * Get nbdossier
     *
     * @return integer 
     */
    public function getNbdossier()
    {
        return $this->nbdossier;
    }

    /**
     * Set datenaiss
     *
     * @param \DateTime $datenaiss
     * @return User
     */
    public function setDatenaiss($datenaiss)
    {
        $this->datenaiss = $datenaiss;

        return $this;
    }

    /**
     * Get datenaiss
     *
     * @return \DateTime 
     */
    public function getDatenaiss()
    {
        return $this->datenaiss;
    }

    /**
     * Set sexe
     *
     * @param boolean $sexe
     * @return User
     */
    public function setSexe($sexe)
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * Get sexe
     *
     * @return boolean 
     */
    public function getSexe()
    {
        return $this->sexe;
    }

    /**
     * Set nbticket
     *
     * @param integer $nbticket
     * @return User
     */
    public function setNbticket($nbticket)
    {
        $this->nbticket = $nbticket;

        return $this;
    }

    /**
     * Get nbticket
     *
     * @return integer 
     */
    public function getNbticket()
    {
        return $this->nbticket;
    }

    /**
     * Set nbgaim
     *
     * @param integer $nbgaim
     * @return User
     */
    public function setNbgaim($nbgaim)
    {
        $this->nbgaim = $nbgaim;

        return $this;
    }

    /**
     * Get nbgaim
     *
     * @return integer 
     */
    public function getNbgaim()
    {
        return $this->nbgaim;
    }

    /**
     * Set soldeprincipal
     *
     * @param integer $soldeprincipal
     * @return User
     */
    public function setSoldeprincipal($soldeprincipal)
    {
        $this->soldeprincipal = $soldeprincipal;

        return $this;
    }

    /**
     * Get soldeprincipal
     *
     * @return integer 
     */
    public function getSoldeprincipal()
    {
        return $this->soldeprincipal;
    }

    public function setVille(Ville $ville = null):self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    /**
     * Set soldetransit
     *
     * @param integer $soldetransit
     * @return User
     */
    public function setSoldetransit($soldetransit)
    {
        $this->soldetransit = $soldetransit;

        return $this;
    }

    /**
     * Get soldetransit
     *
     * @return integer 
     */
    public function getSoldetransit()
    {
        return $this->soldetransit;
    }

    /**
     * Set soldegain
     *
     * @param integer $soldegain
     * @return User
     */
    public function setSoldegain($soldegain)
    {
        $this->soldegain = $soldegain;

        return $this;
    }

    /**
     * Get soldegain
     *
     * @return integer 
     */
    public function getSoldegain()
    {
        return $this->soldegain;
    }


    /**
     * Set formateur
     *
     * @param boolean $formateur
     * @return User
     */
    public function setFormateur($formateur)
    {
        $this->formateur = $formateur;

        return $this;
    }

    /**
     * Get formateur
     *
     * @return boolean 
     */
    public function getFormateur()
    {
        return $this->formateur;
    }

    public function setImgcouverture(Imgcouverture $imgcouverture = null): self
    {
        $this->imgcouverture = $imgcouverture;

        return $this;
    }

    public function getImgcouverture(): ?Imgcouverture
    {
        return $this->imgcouverture;
    }

    /**
     * Set diplome
     *
     * @param string $diplome
     * @return User
     */
    public function setDiplome($diplome)
    {
        $this->diplome = $diplome;

        return $this;
    }

    /**
     * Get diplome
     *
     * @return string 
     */
    public function getDiplome()
    {
        return $this->diplome;
    }

    /**
     * Set poste
     *
     * @param string $poste
     * @return User
     */
    public function setPoste($poste)
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * Get poste
     *
     * @return string 
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return User
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set telpublic
     *
     * @param boolean $telpublic
     * @return User
     */
    public function setTelpublic($telpublic)
    {
        $this->telpublic = $telpublic;

        return $this;
    }

    /**
     * Get telpublic
     *
     * @return boolean 
     */
    public function getTelpublic()
    {
        return $this->telpublic;
    }

    /**
     * Set mailformateur
     *
     * @param string $mailformateur
     * @return User
     */
    public function setMailformateur($mailformateur)
    {
        $this->mailformateur = $mailformateur;

        return $this;
    }

    /**
     * Get mailformateur
     *
     * @return string 
     */
    public function getMailformateur()
    {
        return $this->mailformateur;
    }
	
    /**
     * Set prenom
     *
     * @param string $prenom
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }
}
