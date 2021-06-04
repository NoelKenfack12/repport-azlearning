<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatorfile\Yourfile;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Repository\Produit\Produit\CorrectionexerciceRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Correctionexercice
 *
 * @ORM\Table("correctionexercice")
 * @ORM\Entity(repositoryClass=CorrectionexerciceRepository::class)
 *  @ApiResource(
 *    normalizationContext={"groups"={"correctionexercice:read"}},
 *    denormalizationContext={"groups"={"correctionexercice:write"}}
 * )
 ** @ORM\HasLifecycleCallbacks
 */
class Correctionexercice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"composquestionnaire:read"})
     */
    private $id;

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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbtele", type="integer")
     */
    private $nbtele;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint")
     */
    private $timestamp;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="mykey", type="integer",nullable=true)
     */
    private $mykey;
	
	/**
	*@Yourfile(taillemax=100000000, message="la taille de l'image  %string% est grande.")
	*/
	private $file;
	
	// permet le stocage temporaire du nom du fichier
	private $tempFilename;
	
	// variable du service de normalisation des noms de fichier
	private $servicetext;
	
	public function __construct()
	{
	$this->src ="source";
	$this->alt ="alternatif";
	$this->nbtele = 0;
	$this->date = new \Datetime();
	$this->timestamp = time();
	$this->mykey = mt_rand(10000,20000);
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
     * @return Correctionexercice
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
     * @return Correctionexercice
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
     * Set date
     *
     * @param \DateTime $date
     * @return Correctionexercice
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
     * Set nbtele
     *
     * @param integer $nbtele
     * @return Correctionexercice
     */
    public function setNbtele($nbtele)
    {
        $this->nbtele = $nbtele;

        return $this;
    }

    /**
     * Get nbtele
     *
     * @return integer 
     */
    public function getNbtele()
    {
        return $this->nbtele;
    }

    /**
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Correctionexercice
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return integer 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
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
	return 'bundles/produit/produit/correction/exercice';
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
	$oldFile = $this->getUploadRootDir().'/'.$this->id.'.Key.'.$this->getMykey().'.'.$this->tempFilename;
	if (file_exists($oldFile)) {
	unlink($oldFile);
	}
	}
	$this->file->move( $this->getUploadRootDir(), $this->id.'.Key.'.$this->getMykey().'.'.$this->src);
	}
	
	/**
	*@ORM\PreRemove()
	*/
	public function preRemoveUpload()
	{
	$this->tempFilename = $this->getUploadRootDir().'/'.$this->id.'.Key.'.$this->getMykey().'.'.$this->src;
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
	
	public function getWebpath()
	{
	return $this->getUploadDir().'/'.$this->getId().'.Key.'.$this->getMykey().'.'.$this->getSrc();
	}

    /**
     * Set mykey
     *
     * @param integer $mykey
     * @return Correctionexercice
     */
    public function setMykey($mykey)
    {
        $this->mykey = $mykey;

        return $this;
    }

    /**
     * Get mykey
     *
     * @return integer 
     */
    public function getMykey()
    {
        return $this->mykey;
    }
}
