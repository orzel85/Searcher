<?php

namespace App\AppBundle\Service;

use App\AppBundle\Helper\LinkParametersBuilder;
use App\AppBundle\Service\Provider\Controller as ProviderController;

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
    }
    
    private function createLinks() {
        $linkParametersBuilder = new LinkParametersBuilder();
        $linkParametersBuilder->setPage($this->page);
        $linkParametersBuilder->setQuery($this->query);
        
        $linkParametersBuilder->setProvider(ProviderController::TORRENTHOUND);
        $links[] = 'http://torrent.localhost/api/torrents.json?' . $linkParametersBuilder->getParamsAsString();
        var_dump($links);
        die();
        
    }
    
    public function getTorrentsAsArray() {
        
    }

    
    
}

