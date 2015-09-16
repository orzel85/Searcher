<?php

namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Torrents
 *
 * @ORM\Table(name="torrents", indexes={@ORM\Index(name="link_sha1", columns={"link_sha1"})})
 * @ORM\Entity(repositoryClass="App\AppBundle\Repository\TorrentsRepository")
 */
class Torrents
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=400, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=400, nullable=false)
     */
    private $link;

    /**
     * @var string
     *
     * @ORM\Column(name="link_sha1", type="string", length=400, nullable=false)
     */
    private $linkSha1;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=50, nullable=false)
     */
    private $provider;

    /**
     * @var integer
     *
     * @ORM\Column(name="seeds", type="integer", nullable=false)
     */
    private $seeds;

    /**
     * @var integer
     *
     * @ORM\Column(name="peers", type="integer", nullable=false)
     */
    private $peers;

    /**
     * @var float
     *
     * @ORM\Column(name="size", type="float", nullable=false)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="sizeOriginal", type="string", nullable=false)
     */
    private $sizeOriginal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set name
     *
     * @param string $name
     * @return Torrents
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Torrents
     */
    public function setLink($link)
    {
        $this->link = $link;
        $this->linkSha1 = sha1($link);
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
     * Set linkSha1
     *
     * @param string $linkSha1
     * @return Torrents
     */
    public function setLinkSha1($linkSha1)
    {
        $this->linkSha1 = $linkSha1;

        return $this;
    }

    /**
     * Get linkSha1
     *
     * @return string 
     */
    public function getLinkSha1()
    {
        return $this->linkSha1;
    }

    /**
     * Set seeds
     *
     * @param integer $seeds
     * @return Torrents
     */
    public function setSeeds($seeds)
    {
        $this->seeds = $seeds;

        return $this;
    }

    /**
     * Get seeds
     *
     * @return integer 
     */
    public function getSeeds()
    {
        return $this->seeds;
    }

    /**
     * Set peers
     *
     * @param integer $peers
     * @return Torrents
     */
    public function setPeers($peers)
    {
        $this->peers = $peers;

        return $this;
    }

    /**
     * Get peers
     *
     * @return integer 
     */
    public function getPeers()
    {
        return $this->peers;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Torrents
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Torrents
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
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
    
    function getProvider() {
        return $this->provider;
    }

    function setProvider($provider) {
        $this->provider = $provider;
    }

    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
    }

    public function getSizeOriginal() {
        return $this->sizeOriginal;
    }

    public function setSizeOriginal($sizeOriginal) {
        $this->sizeOriginal = $sizeOriginal;
    }

}
