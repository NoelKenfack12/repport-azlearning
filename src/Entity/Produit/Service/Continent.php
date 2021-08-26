<?php

namespace App\Entity\Produit\Service;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Service\Servicetext\GeneralServicetext;
use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Validator\Validatortext\Siteweb;
use App\Repository\Produit\Service\ContinentRepository;
use App\Entity\Produit\Service\Pays;
use Doctrine\Common\Collections\Collection;


/**
 * Continent
 *
 * @ORM\Table("continent")
 * @ORM\Entity(repositoryClass=ContinentRepository::class)
  * @UniqueEntity(fields="nom", message="Ce continent existe déjà.")
  * @UniqueEntity(fields="citoyen", message="Ce continent existe déjà.")
  * @UniqueEntity(fields="siteweb", message="Ce site est déjà enregistré.")
  ** @ORM\HasLifecycleCallbacks
 */
class Continent
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
     * @var string
     *
     * @ORM\Column(name="siteweb", type="string", length=255,unique=true,nullable=true)
     *@Siteweb()
     */
    private $siteweb;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=100,unique=true)
     *@Taillemin(valeur=4, message="Au moins 4 caractères")
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $nom;
	
	/**
     * @var string
     *
     * @ORM\Column(name="citoyen", type="string", length=100,unique=true, nullable=true)
     *@Taillemin(valeur=4, message="Au moins 4 caractères")
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $citoyen;
	
	/**
     * @var string
     *
     * @ORM\Column(name="citoyenne", type="string", length=100,unique=true, nullable=true)
     *@Taillemin(valeur=4, message="Au moins 4 caractères")
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $citoyenne;
	
	/**
     * @ORM\OneToMany(targetEntity=Pays::class, mappedBy="continent")
     */
    private $pays;
	
	/**
	*@Image(taillemax=1500000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	private $serviceaccent;

    private $em;
	
	public function __construct(GeneralServicetext $service)
	{
	$this->serviceaccent = $service ;
	$this->pays = new \Doctrine\Common\Collections\ArrayCollection();
	$this->src ="source";
	$this->alt ="alternatif";
	}
	
	public function getServiceaccent()
	{
	return $this->serviceaccent;
	}
	public function setServiceaccent(GeneralServicetext $service)
	{
	$this->serviceaccent = $service;
	}

    public function getEm()
	{
	return $this->em;
	}
	public function setEm($em)
	{
	$this->em = $em;
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
     * @return Imgslide
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
     * @return Imgslide
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
     * Set nom
     *
     * @param string $nom
     * @return Continent
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function premajuscule()
	{
	$text = $this->serviceaccent->retireAccent($this->nom);
	$text2 = $this->serviceaccent->retireAccent($this->citoyen);
	$this->nom = strtoupper($text);
	$this->citoyen = strtoupper($text2);
	}

    /**
     * Get pays
     * 
     */
    public function getPays(): ?Collection
    {
        return $this->pays;
    }


    /**
     * Set citoyen
     *
     * @param string $citoyen
     * @return Continent
     */
    public function setCitoyen($citoyen)
    {
        $this->citoyen = $citoyen;

        return $this;
    }

    /**
     * Get citoyen
     *
     * @return string 
     */
    public function getCitoyen()
    {
        return $this->citoyen;
    }

    /**
     * Set citoyenne
     *
     * @param string $citoyenne
     * @return Continent
     */
    public function setCitoyenne($citoyenne)
    {
        $this->citoyenne = $citoyenne;

        return $this;
    }

    /**
     * Get citoyenne
     *
     * @return string 
     */
    public function getCitoyenne()
    {
        return $this->citoyenne;
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
	return 'bundles/produit/service/images/continent';
	}

	protected function getUploadRootDir()
	{
	// On retourne le chemin relatif vers l'image pour notre codePHP
	return  __DIR__.'/../../../../web/'.$this->getUploadDir();
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
        $this->src = $this->serviceaccent->normaliseText($text);
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
     * Add pays
     *
     * @return Continent
     */
    public function addPay(Pays $pays): self
    {
        $this->pays[] = $pays;

        return $this;
    }

    /**
     * Remove pays
     *
     */
    public function removePay(Pays $pays)
    {
        $this->pays->removeElement($pays);
    }
	
	/**
     * Set siteweb
     *
     * @param string $siteweb
     * @return Continent
     */
    public function setSiteweb($siteweb)
    {
        $this->siteweb = $siteweb;

        return $this;
    }

    /**
     * Get siteweb
     *
     * @return string 
     */
    public function getSiteweb()
    {
        return $this->siteweb;
    }

    public function listePays()
    {
        $liste_pays = $this->em->getRepository(Pays::class)
								->findBy(array('continent'=>$this), array('nom'=>'asc'));
        return $liste_pays;
    }
}
