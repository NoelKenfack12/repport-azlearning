<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use App\Repository\Produit\Service\AproposRepository;
use App\Entity\Users\User\User;

/**
 * Apropos
 *
 * @ORM\Table("apropos")
 * @ORM\Entity(repositoryClass=AproposRepository::class)
 ** @ORM\HasLifecycleCallbacks
 */
 
class Apropos
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
     * @var boolean
     *
     * @ORM\Column(name="entreprise", type="boolean")
     */
    private $entreprise;

    /**
     * @var boolean
     *
     * @ORM\Column(name="particulier", type="boolean")
     */
    private $particulier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="team", type="boolean")
     */
    private $team;

    /**
     * @var boolean
     *
     * @ORM\Column(name="slider", type="boolean")
     */
    private $slider;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     */
    private $nom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="poste", type="string", length=255,nullable=true)
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     */
    private $poste;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=600, message="Au plus 600 caractès")
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
     * @ORM\Column(name="facebook", type="string", length=255,nullable=true)
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     */
    private $facebook;

    /**
     * @var string
     *
     * @ORM\Column(name="twitter", type="string", length=255,nullable=true)
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     */
    private $twitter;

    /**
     * @var string
     *
     * @ORM\Column(name="google", type="string", length=255,nullable=true)
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     */
    private $google;
	
	/**
      * @ORM\ManyToOne(targetEntity=User::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $user;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=255,nullable=true)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255,nullable=true)
     */
    private $alt;
	
	/**
	*@Image(taillemax=1500000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->src ="source";
		$this->alt ="alternatif";
		$this->entreprise = false;
		$this->particulier = false;
		$this->team = false;
		$this->rang = 0;
		$this->slider = false;
		$this->date = new \Datetime();
		$this->servicetext = $service;
	}
	
	public function getServicetext()
	{
		return $this->servicetext;
	}
	
	public function setServicetext(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		return $this;
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
     * Set entreprise
     *
     * @param boolean $entreprise
     * @return Apropos
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return boolean 
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set particulier
     *
     * @param boolean $particulier
     * @return Apropos
     */
    public function setParticulier($particulier)
    {
        $this->particulier = $particulier;

        return $this;
    }

    /**
     * Get particulier
     *
     * @return boolean 
     */
    public function getParticulier()
    {
        return $this->particulier;
    }

    /**
     * Set team
     *
     * @param boolean $team
     * @return Apropos
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return boolean 
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set slider
     *
     * @param boolean $slider
     * @return Apropos
     */
    public function setSlider($slider)
    {
        $this->slider = $slider;

        return $this;
    }

    /**
     * Get slider
     *
     * @return boolean 
     */
    public function getSlider()
    {
        return $this->slider;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Apropos
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
     * Set contenu
     *
     * @param string $contenu
     * @return Apropos
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
     * @return Apropos
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
     * Set facebook
     *
     * @param string $facebook
     * @return Apropos
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * Get facebook
     *
     * @return string 
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     * @return Apropos
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * Get twitter
     *
     * @return string 
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set google
     *
     * @param string $google
     * @return Apropos
     */
    public function setGoogle($google)
    {
        $this->google = $google;

        return $this;
    }

    /**
     * Get google
     *
     * @return string 
     */
    public function getGoogle()
    {
        return $this->google;
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
	
	public function getUploadDir()
	{
	// On retourne le chemin relatif vers l'image pour un navigateur
	return 'bundles/produit/service/images/apropos';
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
	$text1 = $this->servicetext->retireAccent($this->nom);
	$text1 = strtolower($text1);
	$this->nom = ucwords($text1);
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
     * Set src
     *
     * @param string $src
     * @return Apropos
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
     * @return Apropos
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

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set poste
     *
     * @param string $poste
     * @return Apropos
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
     * Set rang
     *
     * @param integer $rang
     * @return Apropos
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
}
