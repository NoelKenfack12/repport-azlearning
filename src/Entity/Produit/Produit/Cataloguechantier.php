<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\Produit\Produit\CataloguechantierRepository;
use App\Entity\Users\User\User;

use App\Validator\Validatorfile\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Cataloguechantier
 *
 * @ORM\Table("cataloguechantier")
 * @ORM\Entity(repositoryClass=CataloguechantierRepository::class)
 * @UniqueEntity(fields="nom", message="Cette catégorie existe déjà.")
 *  @ApiResource(
 *    normalizationContext={"groups"={"cataloguechantier:read"}},
 *    denormalizationContext={"groups"={"cataloguechantier:write"}}
 * )
 ** @ORM\HasLifecycleCallbacks
 */
 
class Cataloguechantier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"cataloguechantier:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255,unique=true)
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=70, message="Au plus 70 caractès")
     * @Groups({"cataloguechantier:read", "cataloguechantier:write"})
     */
    private $nom;
	
	/**
     * @var text
     *
     * @ORM\Column(name="description", type="text")
     *@Taillemin(valeur=3, message="Au moins 3 caractères")
     *@Taillemax(valeur=500, message="Au plus 500 caractès")
     */
    private $description;
	
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
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
		$this->src = "source";
		$this->alt = "alternatif";
		$this->rang = 0;
		$this->servicetext = $service;
		$this->date = new \Datetime();
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
     * Set nom
     *
     * @param string $nom
     * @return Cataloguechantier
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
     * Set description
     *
     * @param string $description
     * @return Cataloguechantier
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
     * Set date
     *
     * @param \DateTime $date
     * @return Cataloguechantier
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
	return 'bundles/produit/produit/images/cataloguechantier';
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
     * Set rang
     *
     * @param integer $rang
     * @return Cataloguechantier
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
     * @return Cataloguechantier
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
     * @return Cataloguechantier
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
}
