<?php

namespace App\AppBundle\Service;



class TorrentsList {
    
    /*
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     *
     * @var \App\AppBundle\Entity\Query 
     */
    private $query;
    
    /**
     *
     * @var \App\AppBundle\Repository\QueryRepository
     */
    private $queryRepository;
    
    private $torrentsList;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function getList() {
        if($this->checkIfLinksOnListValid()) {
            return $this->torrentsList;
        }
        $this->torrentsList = $this->getLinksFromExternalSystems();
        $this->updateListOfLinksInDb();
        return $this->torrentsList;
    }
    
    /**
     * Getting links from database
     */
    private function getLinksFromDb() {
        
    }
    
    /**
     * Gettings links from external torrent system, like torrenthoud, isohun etc.
     */
    private function getLinksFromExternalSystems() {
        
    }
    
    /**
     * Checks if links getted from are valid and can be send to user.
     */
    private function checkIfLinksOnListValid() {
        $torrentsList = $this->getLinksFromDb();
    }
    
    /**
     * Updating links in database using links getted from external system.
     */
    private function updateListOfLinksInDb() {
        
    }
    
    function getQuery() {
        return $this->query;
    }

    function setQuery(\App\AppBundle\Entity\Query $query) {
        $this->query = $query;
    }


    
}