<?php

namespace App\AppBundle\Service;

use App\AppBundle\Helper\LinkParametersBuilder;
use App\AppBundle\Service\Provider\Controller as ProviderController;
use App\AppBundle\Service\Curl;

class GetAllTorrents {
    
    private $query;
    
    private $page;
    
    private $torrentsList;
    
    public function __construct() {
        
    }
    
    public function getQuery() {
        return $this->query;
    }

    public function getPage() {
        return $this->page;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    public function setPage($page) {
        $this->page = $page;
    }
    
    public function execute() {
        $linksArray = $this->createLinks();
        $curl = new Curl();
        $result = $curl->curlMultiSend($linksArray);
        $this->torrentsList = $this->convertResultToArray($result);
    }
    
    private function convertResultToArray($result) {
        $return = array();
        $tempArray = array();
        foreach($result as $r) {
            $tempArray = json_decode($r);
            $return = array_merge($return, $tempArray);
        }
        return $return;
    }
    
    private function createLinks() {
        $linkParametersBuilder = new LinkParametersBuilder();
        $linkParametersBuilder->setPage($this->page);
        $linkParametersBuilder->setQuery($this->query);
        
        $linkParametersBuilder->setProvider(ProviderController::TORRENTHOUND);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::TORRENTDOWNLOADS);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::BITSNOOP);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::PIRATEBAYORG);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::TORRENTREACTOR);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::EXTRATORRENT);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::KICKASSTORRENT);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::MONONOVA);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::LIMETORRENTS);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        
        $linkParametersBuilder->setProvider(ProviderController::ISOHUNT);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        return $links;
        
    }
    
    public function getTorrentsAsArray() {
        return $this->convertArrayTorrentObjToArray($this->torrentsList);
    }
    
    private function convertArrayTorrentObjToArray($list) {
        $collection = array();
        $return = array();
        $counter = 0;
        foreach($list as $el) {
            $collection[] = $this->convertTorrentObjToArray($el);
            $counter++;
        }
        $return['count'] = $counter;
        $return['collection'] = $collection;
        return $return;
    }
    
    private function convertTorrentObjToArray($obj) {
        $return = array();
        $return['name'] = $obj->name;
        $return['link'] = $obj->link;
        $return['linkSha1'] = $obj->linkSha1;
        $return['provider'] = $obj->provider;
        $return['seeds'] = $obj->seeds;
        $return['peers'] = $obj->peers;
        $return['size'] = $obj->size;
        $return['sizeOriginal'] = $obj->sizeOriginal;
        $return['createDate'] = $obj->createDate;
        $return['updateDate'] = $obj->updateDate;
        return $return;
    }
    
}

