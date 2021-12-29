<?php
namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatorfile\Yourfile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\General\Validatortext\Taillemin;
use App\Validator\General\Validatortext\Taillemax;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToArrayTransformer;

use App\Validator\Validatortext\Email;
use App\Validator\Validatortext\Telephone;
use App\Repository\Produit\Service\RecrutementRepository;
use App\Entity\Users\User\User;
use App\Entity\Produit\Service\Yourcv;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Recrutement
 *
 * @ORM\Table("recrutement")
 * @ORM\Entity(repositoryClass=RecrutementRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"recrutement:read"}},
 *    denormalizationContext={"groups"={"recrutement:write"}}
 * )
 ** @ORM\HasLifecycleCallbacks
 */
class Recrutement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"recrutement:read"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=255)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255)
     */
    private $alt;
	
	/**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;
	
	/**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=true)
      *@Email()
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="typedocument", type="text")
     */
    private $typedocument;
	
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
     * @ORM\Column(name="moyentransfert", type="string", length=255, nullable=true)
     */
    private $moyentransfert;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="montantransfert", type="integer", length=255, nullable=true)
     */
    private $montantransfert;
	
	/**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="valide", type="boolean")
     */
    private $valide;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="dossierformateur", type="boolean")
     */
    private $dossierformateur;

    /**
     * @var boolean
     *
     * @ORM\Column(name="supportformation", type="boolean")
     */
    private $supportformation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="fichepaiement", type="boolean")
     */
    private $fichepaiement;
	
	/**
     * @var text
     *
     * @ORM\Column(name="villeactuel", type="text", nullable=true)
     */
    private $villeactuel;
	
	/**
     * @var text
     *
     * @ORM\Column(name="pays", type="text", nullable=true)
     */
    private $pays;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
    * @ORM\OneToOne(targetEntity=Yourcv::class, cascade={"persist","remove"})
	* @ORM\JoinColumn(nullable=true)
	*@Assert\Valid()
    */
	private $yourcv;
	
	
	/**
	*@Yourfile(taillemax=1500000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	// variable du service de normalisation des noms de fichier
	private $servicetext;
	
	public function __construct(GeneralServicetext $servicetext)
	{
		$this->src ="source";
		$this->alt ="alternatif";
		$this->valide = false;
		$this->dossierformateur = false;
		$this->servicetext = $servicetext;
		$this->date = new \Datetime();
        $this->supportformation = false;
        $this->fichepaiement = false;
        $this->typedocument = 'depot';
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
     * Set src
     *
     * @param string $src
     * @return Recrutement
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Get src
     *
     * @return string 
     */
    public function getSrc()
    {
        return $this->src;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Recrutement
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }
	
	//permet la récupération du nom du fichier temporaire
    public function getTempFilename()
    {
    return $this->tempFilename;
    }
	//permet de modifier le contenu de la variable tempFilename
    public function setTempFilename($temp)
	{
	$this->tempFilename=$temp;
	}
	// permet la récupération du nom du fiechier
	public function getFile()
	{
	return $this->file;
	}
	
	public function setServicetext( GeneralServicetext $service)
	{
	$this->servicetext = $service;
	}
	public function getServicetext()
	{
	return $this->servicetext;
	}
	public function getUploadDir()
	{
	// On retourne le chemin relatif vers l'image pour un navigateur
	return 'bundles/produit/service/images/recrutement';
	}
	protected function getUploadRootDir()
	{
	// On retourne le chemin relatif vers l'image pour notre codePHP
	return  __DIR__.'/../../../../public/'.$this->getUploadDir();
	}
	
	public function setFile(UploadedFile $file)
	{
	$this->file = $file;
	// On vérifie si on avait déjà un fichier pour cette entité
	if (null !== $this->src) {
	// On sauvegarde l'extension du fichier pour le supprimer plus tard
	$this->tempFilename = $this->src;
	// On réinitialise les valeurs des attributs url et alt
	$this->src = null;
	$this->alt = null;
	}
	}
	
	/**
	* @ORM\PrePersist()
	* @ORM\PreUpdate()
	*/
	public function preUpload()
	{
        if (null === $this->file) {
            return;
        }
        $text = $this->file->getClientOriginalName();
        $this->src = $this->servicetext->normaliseText($text);
        $this->alt = $this->src;
	}
	/**
	* @ORM\PostPersist()
	* @ORM\PostUpdate()
	*/
	public function upload()
	{
	// Si jamais il n'y a pas de fichier (champ facultatif)
	if (null === $this->file) {
	    return;
	}
	if (null !== $this->tempFilename) {
        $oldFile = $this->getUploadRootDir().'/'.$this->id.'.'.$this->tempFilename;
        if (file_exists($oldFile)) {
            unlink($oldFile);
        }
	}
	$this->file->move( $this->getUploadRootDir(), $this->id.'.'.$this->src);
	}

	/**
	*@ORM\PreRemove()
	*/
	public function preRemoveUpload()
	{
	$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.'.$this->src;
	}
	/**
	* @ORM\PostRemove()
	*/
	public function postRemoveUpload()
	{
	// En PostRemove, on n'a pas accès à l'id, on utilise notre nom sauvegardé
	if (file_exists($this->tempFilename)) {
	// On supprime le fichier
	unlink($this->tempFilename);
	}
	}
	public function getWebPath()
	{
	return $this->getUploadDir().'/'.$this->getId().'.'.$this->getSrc();
	}

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Recrutement
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
     * Set message
     *
     * @param string $message
     * @return Recrutement
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
     * Set typedocument
     *
     * @param string $typedocument
     * @return Recrutement
     */
    public function setTypedocument($typedocument)
    {
        $this->typedocument = $typedocument;

        return $this;
    }

    /**
     * Get typedocument
     *
     * @return string 
     */
    public function getTypedocument()
    {
        return $this->typedocument;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
	
	public function numFacture()
	{
		$datetransform = new DateTimeToArrayTransformer();
		$dt = $datetransform->transform($this->getDate());
		return ''.$dt['day'].''.$dt['month'].''.$this->getId().''.$dt['year'];
	}
	
	public function dateFacture()
	{
		$datetransform = new DateTimeToArrayTransformer();
		$dt = $datetransform->transform($this->getDate());
		return $dt['day'].'-'.$dt['month'].'-'.$dt['year'];
	}
	
	
	//gestion de la fiche d'ouverture de dossier.
	public function getUploadDossierDir()
	{
	// On retourne le chemin relatif vers l'image pour un navigateur
	return 'bundles/produit/service/facture/dossier';
	}
	
	public function getUploadDossierRootDir()
	{
	// On retourne le chemin relatif vers l'image pour notre codePHP
	return  __DIR__.'/../../../../public/'.$this->getUploadDossierDir();
	}
	
	public function getDossierWebPath()
	{
	return $this->getUploadDossierDir().'/'.$this->numFacture().'.pdf';
	}

    /**
     * Set mail
     *
     * @param string $mail
     * @return Recrutement
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return Recrutement
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
     * Set username
     *
     * @param string $username
     * @return Recrutement
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

    /**
     * Set villeactuel
     *
     * @param string $villeactuel
     * @return Recrutement
     */
    public function setVilleactuel($villeactuel)
    {
        $this->villeactuel = $villeactuel;

        return $this;
    }

    /**
     * Get villeactuel
     *
     * @return string 
     */
    public function getVilleactuel()
    {
        return $this->villeactuel;
    }

    /**
     * Set moyentransfert
     *
     * @param string $moyentransfert
     * @return Recrutement
     */
    public function setMoyentransfert($moyentransfert)
    {
        $this->moyentransfert = $moyentransfert;

        return $this;
    }

    /**
     * Get moyentransfert
     *
     * @return string 
     */
    public function getMoyentransfert()
    {
        return $this->moyentransfert;
    }

    /**
     * Set montantransfert
     *
     * @param integer $montantransfert
     * @return Recrutement
     */
    public function setMontantransfert($montantransfert)
    {
        $this->montantransfert = $montantransfert;

        return $this;
    }

    /**
     * Get montantransfert
     *
     * @return integer 
     */
    public function getMontantransfert()
    {
        return $this->montantransfert;
    }

    /**
     * Set valide
     *
     * @param boolean $valide
     * @return Recrutement
     */
    public function setValide($valide)
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * Get valide
     *
     * @return boolean 
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Recrutement
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
     * Set dossierformateur
     *
     * @param boolean $dossierformateur
     * @return Recrutement
     */
    public function setDossierformateur($dossierformateur)
    {
        $this->dossierformateur = $dossierformateur;

        return $this;
    }

    /**
     * Get dossierformateur
     *
     * @return boolean 
     */
    public function getDossierformateur()
    {
        return $this->dossierformateur;
    }
    
    /**
     * Set supportformation
     *
     * @param boolean $supportformation
     * @return Recrutement
     */
    public function setSupportformation($supportformation)
    {
        $this->supportformation = $supportformation;

        return $this;
    }

    /**
     * Get supportformation
     *
     * @return boolean 
     */
    public function getSupportformation()
    {
        return $this->supportformation;
    }

    

    /**
     * Set fichepaiement
     *
     * @param boolean $fichepaiement
     * @return Recrutement
     */
    public function setFichepaiement($fichepaiement)
    {
        $this->fichepaiement = $fichepaiement;

        return $this;
    }

    /**
     * Get fichepaiement
     *
     * @return boolean 
     */
    public function getFichepaiement()
    {
        return $this->fichepaiement;
    }

    public function setYourcv(Yourcv $yourcv = null): self
    {
        $this->yourcv = $yourcv;

        return $this;
    }

    public function getYourcv(): ?Yourcv
    {
        return $this->yourcv;
    }
}
