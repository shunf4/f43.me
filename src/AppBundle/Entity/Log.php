<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *     name="log"
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Log
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="items_number", type="integer")
     */
    protected $itemsNumber;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Feed", inversedBy="logs")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id")
     */
    protected $feed;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Item", inversedBy="logs")
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    protected $item;

    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set itemsNumber.
     *
     * @param int $itemsNumber
     *
     * @return self
     */
    public function setItemsNumber($itemsNumber)
    {
        $this->itemsNumber = $itemsNumber;

        return $this;
    }

    /**
     * Get itemsNumber.
     *
     * @return int $itemsNumber
     */
    public function getItemsNumber()
    {
        return $this->itemsNumber;
    }

    /**
     * Set createdAt.
     *
     * @param string|\DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return string|\DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     */
    public function timestamps()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Return feed.
     *
     * @return Feed
     */
    public function getFeed()
    {
        return $this->feed;
    }
}
