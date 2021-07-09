<?php

namespace App\Entity\Users\User;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Users\User\SujetepingleRepository;
use App\entity\Users\User\User;
use App\entity\Produit\Service\Commentaireblog;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Sujetepingle
 *
 * @ORM\Table("sujetepingle")
 * @ORM\Entity(repositoryClass=SujetepingleRepository::class)
 * @ApiResource(
 *    normalizationContext={"groups"={"sujetepingle:read"}},
 *    denormalizationContext={"groups"={"sujetepingle:write"}}
 * )
 */
class Sujetepingle
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"sujetepingle:read"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
	
	/**
       * @ORM\ManyToOne(targetEntity=User::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $user;
	
	/**
       * @ORM\ManyToOne(targetEntity=Commentaireblog::class)
       * @ORM\JoinColumn(nullable=false)
    */
	private $commentaireblog;
	
	public function __construct()
	{
		$this->date = new \Datetime();
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
     * Set date
     *
     * @param \DateTime $date
     * @return Sujetepingle
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

    public function setUser(User $user):self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setCommentaireblog(Commentaireblog $commentaireblog): self
    {
        $this->commentaireblog = $commentaireblog;

        return $this;
    }

    public function getCommentaireblog(): ?Commentaireblog
    {
        return $this->commentaireblog;
    }
}
