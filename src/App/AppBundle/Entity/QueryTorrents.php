<?php

namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QueryTorrents
 *
 * @ORM\Table(name="query_torrents", indexes={@ORM\Index(name="query_query_sha1", columns={"query_value_sha1"})})
 * @ORM\Entity(repositoryClass="App\AppBundle\Repository\QueryTorrentsRepository")
 */
class QueryTorrents
{
    /**
     * @var string
     *
     * @ORM\Column(name="query_value_sha1", type="string", length=300, nullable=false)
     */
    private $queryValueSha1;

    /**
     * @var string
     *
     * @ORM\Column(name="torrents_link_sha1", type="string", length=400, nullable=false)
     */
    private $torrentsLinkSha1;

    /**
     * @var string
     *
     * @ORM\Column(name="order_on_list", type="integer")
     */
    private $orderOnList;
    
    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=400, nullable=false)
     */
    private $provider;

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="integer", nullable=false)
     */
    private $page;

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
     * Set queryValueSha1
     *
     * @param string $queryValueSha1
     * @return QueryTorrents
     */
    public function setQueryValueSha1($queryValueSha1)
    {
        $this->queryValueSha1 = $queryValueSha1;

        return $this;
    }

    /**
     * Get queryValueSha1
     *
     * @return string 
     */
    public function getQueryValueSha1()
    {
        return $this->queryValueSha1;
    }

    /**
     * Set torrentsLinkSha1
     *
     * @param string $torrentsLinkSha1
     * @return QueryTorrents
     */
    public function setTorrentsLinkSha1($torrentsLinkSha1)
    {
        $this->torrentsLinkSha1 = $torrentsLinkSha1;

        return $this;
    }

    /**
     * Get torrentsLinkSha1
     *
     * @return string 
     */
    public function getTorrentsLinkSha1()
    {
        return $this->torrentsLinkSha1;
    }

    /**
     * Set page
     *
     * @param integer $page
     * @return QueryTorrents
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return QueryTorrents
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
     * @return QueryTorrents
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

    function getOrderOnList() {
        return $this->orderOnList;
    }

    function setOrderOnList($orderOnList) {
        $this->orderOnList = $orderOnList;
    }


    
}
