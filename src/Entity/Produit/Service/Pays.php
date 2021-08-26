<?php

namespace App\Entity\Produit\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Validator\Validatortext\Siteweb;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Produit\Service\PaysRepository;
use App\Entity\Users\User\User;
use App\Entity\Users\User\Contacts;
use App\Entity\Produit\Service\Drapeau;
use App\Entity\Produit\Service\Continent;
use Doctrine\Common\Collections\Collection;

/**
 * Pays
 *
 * @ORM\Table("pays")
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 * @UniqueEntity(fields="siteweb", message="Ce site est déjà enregistré.")
 * @UniqueEntity(fields="nom", message="Ce pays est déjà enregistré.")
 * @UniqueEntity(fields="code", message="Ce code existe déjà.")
 ** @ORM\HasLifecycleCallbacks
 */
 
class Pays
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
     * @ORM\Column(name="nom", type="string", length=255,unique=true)
     *@Taillemin(valeur=4,message="Au moins 4 caractères")
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $nom;

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
     * @ORM\Column(name="citoyen", type="string", length=255,nullable=true)
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $citoyen;
	
	/**
     * @var string
     *
     * @ORM\Column(name="citoyenne", type="string", length=255,nullable=true)
     *@Taillemax(valeur=60, message="Au plus 60 caractères")
     */
    private $citoyenne;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255,unique=true,nullable=true)
     */
    private $code;
	
	/**
     * @var string
     *
     * @ORM\Column(name="domaine", type="string", length=255,unique=true,nullable=true)
     */
    private $domaine;
	
	/**
     * @ORM\OneToOne(targetEntity=Drapeau::class,  cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=true)
     *@Assert\Valid()
    */
	private $drapeau;
	
	/**
	* @ORM\ManyToOne(targetEntity=Continent::class, inversedBy="pays")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $continent;
	
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
	
	// variable du service de normalisation des noms de fichier
	private $servicetext;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="country")
     */
    private $users;
	
	public function __construct(GeneralServicetext $service)
                           	{
                           		$this->servicetext = $service;
                             $this->users = new ArrayCollection();
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
     * @return Pays
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
     * Set siteweb
     *
     * @param string $siteweb
     * @return Pays
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

    /**
     * Set citoyen
     *
     * @param string $citoyen
     * @return Pays
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
     * Set code
     *
     * @param string $citoyen
     * @return Pays
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

	 /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
	
	/**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function premajuscule()
	{
	$this->nom =  ucfirst($this->nom);
	$this->citoyen =  ucfirst($this->citoyen);
	$this->citoyenne = ucfirst($this->citoyenne);
	}


    /**
     * Set citoyenne
     *
     * @param string $citoyenne
     * @return Pays
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
	
	public function name($tail)
                           	{
                           		if(strlen($this->nom) <= $tail)
                           		{
                           		return $this->nom;
                           		}else{
                           		$text = wordwrap($this->nom,$tail,'~',true);
                           		$tab = explode('~',$text);
                           		$text = $tab[0];
                           		return $text.'...';
                           		}
                           	}

    
    /**
     * Set domaine
     *
     * @param string $domaine
     * @return Pays
     */
    public function setDomaine($domaine)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return string 
     */
    public function getDomaine()
    {
        return $this->domaine;
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
	public function setServicetext(GeneralServicetext $service)
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
                           	return 'bundles/produit/service/images/pays';
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
     * @return Pays
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
     * @return Pays
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
     * Set drapeau
     *
     * @return Pays
     */
    public function setDrapeau(Drapeau $drapeau = null)
    {
        $this->drapeau = $drapeau;

        return $this;
    }

    /**
     * Get drapeau
     *
     */
    public function getDrapeau():? Drapeau
    {
        return $this->drapeau;
    }

    /**
     * Set continent
     *
     * @return Pays
     */
    public function setContinent(Continent $continent): self
    {
        $this->continent = $continent;
		$continent->addPay($this);

        return $this;
    }

    public function getContinent(): ?Continent
    {
        return $this->continent;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCountry($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCountry() === $this) {
                $user->setCountry(null);
            }
        }

        return $this;
    }
	
}
