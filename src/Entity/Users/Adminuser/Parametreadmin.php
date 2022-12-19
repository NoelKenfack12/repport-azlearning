<?php

namespace App\Entity\Users\Adminuser;

use Doctrine\ORM\Mapping as ORM;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Users\Adminuser\ParametreadminRepository;
use App\Entity\Users\User\User;

/**
 * Parametreadmin
 *
 * @ORM\Table("parametreadmin")
 * @ORM\Entity(repositoryClass=ParametreadminRepository::class)
  ** @ORM\HasLifecycleCallbacks
 */
 
class Parametreadmin
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
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;
	
	/**
     * @var text
     *
     * @ORM\Column(name="valeur", type="text", nullable=true)
     */
    private $valeur;
	
	/**
     * @var text
     *
     * @ORM\Column(name="link", type="text", nullable=true)
     */
    private $link;
	
	/**
     * @var text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="rang", type="integer", length=255)
     */
    private $rang;
	
	/**
     * @var string
     *
     * @ORM\Column(name="src", type="string", length=255, nullable=true)
     */
    private $src;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;
	
	/**
      * @ORM\ManyToOne(targetEntity=User::class)
      * @ORM\JoinColumn(nullable=false)
      */
    private $user;
	
	/**
	*@Image(taillemax=1500000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	// variable du service de normalisation des noms des pays.
	private $servicetext;
	
	public function __construct(GeneralServicetext $service)
	{
		$this->servicetext = $service;
		$this->rang = 1;
		$this->date = new \Datetime();
	}

	public function setServicetext( GeneralServicetext $service)
    {
		$this->servicetext = $service;
    }
    public function getServicetext()
    {
		return $this->servicetext;
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
     * Set type
     *
     * @param string $type
     * @return Parametreadmin
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Parametreadmin
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
		return 'bundles/users/adminuser/images/parametreadmin';
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
		if (null === $this->file){
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
		if (file_exists($this->tempFilename)){
		// On supprime le fichier
		unlink($this->tempFilename);
		}
	}
	
	public function getWebPath()
	{
		return $this->getUploadDir().'/'.$this->getId().'.'.$this->getSrc();
	}

    /**
     * Set rang
     *
     * @param integer $rang
     * @return Parametreadmin
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
     * Set src
     *
     * @param string $src
     * @return Parametreadmin
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
     * @return Parametreadmin
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

    /**
     * Set user
     *
     * @return Parametreadmin
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Parametreadmin
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Parametreadmin
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set valeur
     *
     * @param string $valeur
     * @return Parametreadmin
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
}
