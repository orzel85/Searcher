<?php

namespace App\AppBundle\Service;

use App\AppBundle\Helper\TorrentList as TorrentsListHelper;
use App\AppBundle\Helper\Filter\SeedsFilter;

class TorrentsList {
    
    /*
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    private $queryFromRequest;
    
    /**
     *
     * @var \App\AppBundle\Entity\Query 
     */
    private $queryDbObject;
    
    /**
     *
     * @var \App\AppBundle\Repository\QueryRepository
     */
    private $queryRepository;
    
    /**
     *
     * @var \App\AppBundle\Repository\QueryTorrentsRepository
     */
    private $queryTorrentsRepository;
    
    /**
     *
     * @var \App\AppBundle\Repository\TorrentsRepository
     */
    private $torrentsRepository;
    
    private $providerCode;
    
    private $page;
    
    private $torrentsList;
    
    /**
     *
     * @var \App\AppBundle\Service\Provider\Controller
     */
    private $torrentController;
    
    public function __construct(\Doctrine\ORM\EntityManager $em, \App\AppBundle\Service\Provider\Controller $torrentController) {
        $this->em = $em;
        $this->queryRepository = $this->em->getRepository('AppBundle:Query');
        $this->queryTorrentsRepository = $this->em->getRepository('AppBundle:QueryTorrents');
        $this->torrentsRepository = $this->em->getRepository('AppBundle:Torrents');
        $this->torrentController = $torrentController;
    }
    
    public function execute() {
        $freshCreated = false;
        if(!$this->checkIfQueryInDb()) {
            $this->createQueryInDb();
            $freshCreated = true;
        }
        if($freshCreated) {
            $torrentsList = $this->getLinksFromExternalSystems();
        }else{
            if($this->checkIfQueryIsValid()) {
                $torrentsList = $this->getLinksFromDb();
            }else{
                $torrentsList = $this->getLinksFromExternalSystems();
                
            }
        }
//        $torrentsList = $this->getLinksFromExternalSystems();
        $torrentsList = SeedsFilter::getLimitedBySeeds($torrentsList, 1);
        $this->torrentsList = $torrentsList;
    }
    
    public function getTorrentsListAsArray() {
        $torrentsListHelper = new TorrentsListHelper();
        $torrentsListHelper->setList($this->torrentsList);
        return $torrentsListHelper->getListAsArray();
    }
    
    public function getTorrentsList() {
        return $this->torrentsList;
    }
    
    private function updateUpdateDateQueryObj() {
        $this->queryRepository->updateUpdateDateToCurrentDate($this->queryDbObject);
    }
    
    private function createQueryInDb() {
        $query = $this->queryRepository->create($this->queryFromRequest, $this->providerCode, $this->page);
        $this->queryDbObject = $query;
    }
    
    private function getLinksFromDb() {
        $queryTorrentsList = $this->queryTorrentsRepository->getTorrentsByQuery($this->queryDbObject, $this->providerCode, $this->page);
        $torrentsLinksSha1Array = $this->getLinksSha1Array($queryTorrentsList);
        $torrentsList = $this->torrentsRepository->getTorrentsByLinkSha1($torrentsLinksSha1Array);
        $sortedTorrentsList = $this->getSortedTorrentsByArray($torrentsList, $torrentsLinksSha1Array);
        return $sortedTorrentsList;
    }
    
    private function getSortedTorrentsByArray($torrentsListToSort, $arrayWithKeysToSort) {
        $return = array();
        foreach($arrayWithKeysToSort as $linkSha1) {
            $return[] = $torrentsListToSort[$linkSha1];
        }
        return $return;
    }
    
    private function getLinksSha1Array(array $queryTorrentsList) {
        $return = array();
        foreach($queryTorrentsList as $queryTorrent) {
            $return[] = $queryTorrent->getTorrentsLinkSha1();
        }
        return $return;
    }
    
    private function getLinksFromExternalSystems() {
        $this->torrentController->setProviderCode($this->providerCode);
        $provider = $this->torrentController->getProvider();
        $provider->setPage($this->page);
        $provider->setQuery($this->queryDbObject->getValue());
        $list = $provider->getTorrentList();
        $this->updateListOfLinksInDb($list);
        $this->updateUpdateDateQueryObj();
        return $list;
    }
    
    private function updateListOfLinksInDb($newTorrentsList) {
        if(!empty($newTorrentsList)) {
            $this->queryTorrentsRepository->updateList($this->queryDbObject, $newTorrentsList, $this->providerCode, $this->page);
            $this->torrentsRepository->updateCreateTorrentList($newTorrentsList);
        }
    }
    
    private function checkIfQueryIsValid() {
        $updateDate = $this->queryDbObject->getUpdateDate();
        $updateDateTs = $updateDate->getTimestamp();
        $days = 5;
        $daysTs = $days * 24 * 3600;
        $validDate = $updateDateTs + $daysTs;
        $currentDate = time();
        if($currentDate > $validDate) {
            return false;
        }
        return true;
    }
    
    private function checkIfQueryInDb() {
        $query = $this->queryRepository->findByQuery($this->queryFromRequest, $this->page, $this->providerCode);
        if(empty($query)) {
            return false;
        }
        $this->queryDbObject = $query;
        return true;
    }
    
    function getQueryFromRequest() {
        return $this->queryFromRequest;
    }

    function setQueryFromRequest($queryFromRequest) {
        $this->queryFromRequest = $queryFromRequest;
    }

    function getProviderCode() {
        return $this->providerCode;
    }

    function getTorrentController() {
        return $this->torrentController;
    }

    function setProviderCode($providerCode) {
        $this->providerCode = $providerCode;
    }

    function setTorrentController(\App\AppBundle\Service\Provider\Controller $torrentController) {
        $this->torrentController = $torrentController;
    }

    public function getPage() {
        return $this->page;
    }

    public function setPage($page) {
        $this->page = $page;
    }

}