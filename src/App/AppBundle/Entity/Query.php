<?php

namespace App\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Query
 *
 * @ORM\Table(name="query")
  * @ORM\Entity(repositoryClass="App\AppBundle\Repository\QueryRepository")
 */
class Query
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=100, nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="value_sha1", type="string", length=200, nullable=true)
     */
    private $valueSha1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=200, nullable=true)
     */
    private $provider;

    /**
     * @var integer
     *
     * @ORM\Column(name="page", type="integer", nullable=false)
     */
    private $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set value
     *
     * @param string $value
     * @return Query
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueSha1
     *
     * @param string $valueSha1
     * @return Query
     */
    public function setValueSha1($valueSha1)
    {
        $this->valueSha1 = $valueSha1;

        return $this;
    }

    /**
     * Get valueSha1
     *
     * @return string 
     */
    public function getValueSha1()
    {
        return $this->valueSha1;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Query
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     * @return Query
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
     * Set provider
     *
     * @param string $provider
     * @return Query
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string 
     */
    public function getProvider()
    {
        return $this->provider;
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
    
    public function getPage() {
        return $this->page;
    }

    public function setPage($page) {
        $this->page = $page;
    }


}
