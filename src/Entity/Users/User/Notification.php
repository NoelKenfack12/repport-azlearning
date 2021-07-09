<?php

namespace App\Entity\Users\User;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Produit\Service\Commentaireblog;
use App\Entity\Users\User\User;
use App\Repository\Users\User\NotificationRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Notification
 *
 * @ORM\Table("notification")
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"notification:read"}},
 *    denormalizationContext={"groups"={"notification:write"}}
 * )
*/

class Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"notification:read"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Groups({"notification:read", "notification:write"})
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text")
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="timestamp", type="bigint")
     */
    private $timestamp;
	
	/**
     * @var boolean
     *
     * @ORM\Column(name="lut", type="boolean")
     */
    private $lut;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
       */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Commentaireblog::class)
       * @ORM\JoinColumn(nullable=true)
       */
	private $commentaireblog;
	
	public function __construct()
	{
		$this->timestamp = time();
		$this->date = new \Datetime();
		$this->lut = false;
	}

	public function getDuree()
	{
	$seconde = time() - $this->timestamp;
	$minute = round($seconde/60);
	if($minute < 60)
	{
	   if($minute < 3)
	   {
		   $duree = 'Il ya un instant';
	   }else{
		   $duree = 'Il ya '.$minute.' min';
	   }
	}else{
	   if($minute < 60*24)
	   {
	   $duree = 'Il ya '.(floor(($minute/60))).'h'.($minute%60).'min';
	   }else{
	      if($minute < 60*24*30)
		  {
		  $reste = $minute - (floor($minute/(60*24)))*(60*24);
		  $duree = 'Il ya '.(floor($minute/(60*24))).'j'.(floor($reste/60)).'h';
		  }else{
		  $nbjour = $this->date->diff(new \Datetime())->days;
		  $age = floor($nbjour/365);
		  if($age < 1)
		  {
			  $reste = $nbjour - (floor($nbjour/(30)))*(30);
			  if(floor($nbjour/(30)) != 0)
			  {
				  $duree = 'Il ya '.(floor($nbjour/(30))).'Mois'.($reste - (floor($reste/30)*30)).'J';
			  }else{
				  $duree = 'Il ya '.($reste - (floor($reste/30)*30)).'J';
			  }
		      
		  }else{
			  $courantyear = date('Y');
			  $duree = $courantyear - $age; 
		  }
		  }
	   }
	}
	return $duree;
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
     * @return Notification
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
     * @return Notification
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
     * @return Notification
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
     * Set timestamp
     *
     * @param integer $timestamp
     * @return Notification
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

    /**
     * Set lut
     *
     * @param boolean $lut
     * @return Notification
     */
    public function setLut($lut)
    {
        $this->lut = $lut;

        return $this;
    }

    /**
     * Get lut
     *
     * @return boolean 
     */
    public function getLut()
    {
        return $this->lut;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setCommentaireblog(Commentaireblog $commentaireblog = null)
    {
        $this->commentaireblog = $commentaireblog;

        return $this;
    }

    public function getCommentaireblog(): ?Commentaireblog
    {
        return $this->commentaireblog;
    }
}
