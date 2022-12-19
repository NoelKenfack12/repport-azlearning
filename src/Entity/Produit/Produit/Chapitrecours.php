<?php
namespace App\Entity\Produit\Produit;

use Doctrine\ORM\Mapping as ORM;
use App\Validator\Validatortext\Taillemin;
use App\Validator\Validatortext\Taillemax;
use App\Service\Servicetext\GeneralServicetext;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Produit\Produit\ChapitrecoursRepository;
use App\Entity\Produit\Produit\Partiecours;
use App\Entity\Produit\Produit\Videochapitre;
use App\Entity\Produit\Produit\Imgchapitre;
use App\Entity\Produit\Produit\Supportchapitre;
use App\Entity\Produit\Produit\Pratiquechapitre;
use App\Entity\Produit\Produit\Exercicepartie;
use App\Entity\Produit\Produit\Animationproduit;
use App\Entity\Produit\Produit\Composexercice;
use App\Entity\Produit\Produit\Compospratique;
use Doctrine\Common\Collections\Collection;
use App\Entity\Produit\Produit\Souscategorie;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Produit\Produit\Composquestionnaire;
use App\Entity\Produit\Produit\Questionnaire;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Chapitrecours
 *
 * @ORM\Table("chapitrecours")
 * @ORM\Entity(repositoryClass=ChapitrecoursRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"chapitrecours:read"}},
 *    denormalizationContext={"groups"={"chapitrecours:write"}}
 * )
 */
class Chapitrecours
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"chapitrecours:read"})
    */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255)
     * @Taillemax(valeur=20000, message="Au plus 20000 caractès")
     * @Groups({"chapitrecours:read", "chapitrecours:write"})
    */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", nullable=true)
     * @Taillemax(valeur=100000, message="Au plus 100000 caractès")
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
     * @ORM\Column(name="rang", type="integer")
     */
    private $rang;
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dureeminute", type="integer", nullable=true)
     */
    private $dureeminute; 
	
	/**
     * @var integer
     *
     * @ORM\Column(name="dureeseconde", type="integer", nullable=true)
     */
    private $dureeseconde; 
	
	/**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;
	
	/**
       * @ORM\ManyToOne(targetEntity=Partiecours::class)
       * @ORM\JoinColumn(nullable=false)
    */
	
	private $partiecours;
	
	/**
      * @ORM\OneToOne(targetEntity=Videochapitre::class, cascade={"persist", "remove"})
      * @Assert\Valid()
    */
   private $videochapitre;
   
   /**
       * @ORM\OneToOne(targetEntity=Imgchapitre::class, cascade={"persist", "remove"})
      * @Assert\Valid()
       */
   private $imgchapitre;
   
   /**
     * @ORM\OneToMany(targetEntity=Supportchapitre::class, mappedBy="chapitrecours")
     */
    private $supportchapitres;
	
	/**
     * @ORM\OneToMany(targetEntity=Pratiquechapitre::class, mappedBy="chapitrecours")
     */
    private $pratiquechapitres;
	
	/**
     * @ORM\OneToMany(targetEntity=Exercicepartie::class, mappedBy="chapitrecours")
     */
    private $exerciceparties;
	
	private $em;

    private $helperService;
	
	public function __construct(GeneralServicetext $service = null)
	{
		$this->date = new \Datetime();
		$this->supportchapitres = new \Doctrine\Common\Collections\ArrayCollection();
		$this->pratiquechapitres = new \Doctrine\Common\Collections\ArrayCollection();
		$this->exerciceparties = new \Doctrine\Common\Collections\ArrayCollection();
		$this->type = 0;
        $this->helperService = $service;
	}

    public function postLoad(LifecycleEventArgs $args)
     {
         $entity = $args->getEntity();
         if(method_exists($entity, 'setGeneralServicetext')) {
             $entity->setGeneralServicetext($this->helperService);
         }
     }
	
	public function getEm()
	{
		return $this->em;
	}
	
	public function setEm($em)
	{
		$this->em = $em;
	}

    public function setGeneralServicetext(GeneralServicetext $service)
     {
        $this->helperService = $service;
     }

     public function setHelperService(GeneralServicetext $service)
     {
        $this->helperService = $service;
     }

     public function getHelperService()
     {
        return $this->helperService;
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
     * @return Chapitrecours
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
     * @return Chapitrecours
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
     * @return Chapitrecours
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
     * Set rang
     *
     * @param integer $rang
     * @return Chapitrecours
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
	
	public function name($tail)
	{
		if(strlen($this->titre) <= $tail)
		{
		    return $this->titre;
		}else{
            $text = wordwrap($this->titre,$tail,'~',true);
            $tab = explode('~',$text);
            $text = $tab[0];
            return $text.'..';
		}
	}

    public function setPartiecours(Partiecours $partiecours): self
    {
        $this->partiecours = $partiecours;

        return $this;
    }

    public function getPartiecours(): ?Partiecours
    {
        return $this->partiecours;
    }

    public function setVideochapitre(Videochapitre $videochapitre = null): self
    {
        $this->videochapitre = $videochapitre;

        return $this;
    }

    public function getVideochapitre(): ?Videochapitre
    {
        return $this->videochapitre;
    }

    public function setImgchapitre(Imgchapitre $imgchapitre = null): self
    {
        $this->imgchapitre = $imgchapitre;

        return $this;
    }

    public function getImgchapitre(): ?Imgchapitre
    {
        return $this->imgchapitre;
    }


    public function addSupportchapitre(Supportchapitre $supportchapitres): self
    {
        $this->supportchapitres[] = $supportchapitres;

        return $this;
    }


    public function removeSupportchapitre(Supportchapitre $supportchapitres)
    {
        $this->supportchapitres->removeElement($supportchapitres);
    }

    public function getSupportchapitres(): ?Collection
    {
        return $this->supportchapitres;
    }

    public function addPratiquechapitre(Pratiquechapitre $pratiquechapitres): self
    {
        $this->pratiquechapitres[] = $pratiquechapitres;

        return $this;
    }

    public function removePratiquechapitre(Pratiquechapitre $pratiquechapitres)
    {
        $this->pratiquechapitres->removeElement($pratiquechapitres);
    }

    public function getPratiquechapitres(): ?Collection
    {
        return $this->pratiquechapitres;
    }

    public function addExerciceparty(Exercicepartie $exerciceparties): self
    {
        $this->exerciceparties[] = $exerciceparties;

        return $this;
    }

    public function removeExerciceparty(Exercicepartie $exerciceparties)
    {
        $this->exerciceparties->removeElement($exerciceparties);
    }

    public function getExerciceparties(): ?Collection
    {
        return $this->exerciceparties;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Chapitrecours
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getQuestionnaires()
    {
        $liste_questionnaire = $this->em->getRepository(Questionnaire::class)
	                                ->findBy(array('chapitrecours'=>$this), array('date'=>'asc'));
        return $liste_questionnaire;
    }

    public function getLectureVideo($user)
    {
        $animationproduit = $this->em->getRepository(Animationproduit::class)
	                                ->findOneBy(array('chapitrecours'=>$this, 'user'=>$user, 'voir'=>1), array('date'=>'asc'), 1);
        if($animationproduit != null)
        {
            return true;
        }else{
            return false;
        }
    }
	
	public function getNoteQuestionnaire($prodpan, $op='voir')
	{
        $oldanimation = $this->em->getRepository(Animationproduit::class)
					         ->findOneBy(array('user'=>$prodpan->getPanier()->getUser(),'chapitrecours'=>$this,'produitpanier'=>$prodpan,'voir'=>1));

        if($oldanimation == null or $oldanimation->getNotechapter() == null)//Si n'a pas encore vue la vidéo ou que ça note est non définie
        {
            $liste_compos_user = $this->em->getRepository(Composquestionnaire::class)
                                    ->myfindComposquestionnaire($prodpan->getId(),$this->getId());
                                
            $liste_questionnaire = $this->em->getRepository(Questionnaire::class)
                                        ->findBy(array('chapitrecours'=>$this, 'valide'=>true), array('date'=>'asc'));
            $nbrequestion = count($liste_questionnaire);
            $servicetext = $this->helperService;
            $trouver = 0;
            foreach($liste_questionnaire as $question)
            {
                $oldcompos = $this->em->getRepository(Composquestionnaire::class)
                                  ->findOneBy(array('produitpanier'=>$prodpan,'questionnaire'=>$question));
                if($oldcompos != null and $oldcompos->getProposition() != null)
                {
                    $bestpro = null;
                    foreach($question->getPropositions() as $prop)
                    {
                        if($prop->getReponse() == true)
                        {
                            $bestpro = $prop;
                        }
                    }
                    
                    if($bestpro == $oldcompos->getProposition())
                    {
                        $trouver = $trouver + 1;
                    }
                }
            }
            if($nbrequestion > 0)
            {
                return ($servicetext->getBareme()*$trouver)/$nbrequestion;
            }else{
                return 0;
            }
        }else{
            return $oldanimation->getNotechapter();
        }
		
		/*
		if($op == "voir")
		{
			$oldlecture = $this->em->getRepository(Animationproduit::class)
							  ->findOneBy(array('produitpanier'=>$prodpan,'chapitrecours'=>$this));
			if($oldlecture != null)
			{
				return 1;
			}else{
				return 0;
			}
		}*/
	}
	
	public function getNoteExercice($prodpan)
	{
		if($prodpan == null)
		{
			return 0;
		}
		$liste_compos_user = $this->em->getRepository(Composexercice::class)
							      ->myfindComposexercice($prodpan->getId(),$this->getId());
								  
		$nbrequestion = count($this->getExerciceparties());
		$servicetext = new GeneralServicetext();
		$point = 0;
		foreach($liste_compos_user as $compos)
		{
			$point = $point + $compos->getNote();
		}
		
		if($nbrequestion > 0)
		{
			return ceil($point/$nbrequestion);
		}else{
			return 0;
		}
	}
	
	public function getNotePratique($prodpan)
	{
		if($prodpan == null)
		{
			return 0;
		}
		$liste_compos_user = $this->em->getRepository(Compospratique::class)
							      ->myfindCompospratique($prodpan->getId(),$this->getId());
								  
		$nbrequestion = count($this->getPratiquechapitres());
		$point = 0;
		foreach($liste_compos_user as $compos)
		{
			$point = $point + $compos->getNote();
		}
		
		if($nbrequestion > 0)
		{
			return ceil($point/$nbrequestion);
		}else{
			return 0;
		}
	}

    /**
     * Set dureeminute
     *
     * @param integer $dureeminute
     * @return Chapitrecours
     */
    public function setDureeminute($dureeminute)
    {
        $this->dureeminute = $dureeminute;

        return $this;
    }

    /**
     * Get dureeminute
     *
     * @return integer 
     */
    public function getDureeminute()
    {
        return $this->dureeminute;
    }

    /**
     * Set dureeseconde
     *
     * @param integer $dureeseconde
     * @return Chapitrecours
     */
    public function setDureeseconde($dureeseconde)
    {
        $this->dureeseconde = $dureeseconde;

        return $this;
    }

    /**
     * Get dureeseconde
     *
     * @return integer 
     */
    public function getDureeseconde()
    {
        return $this->dureeseconde;
    }
}
