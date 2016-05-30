<?php

namespace App\AppBundle\Service;

use App\AppBundle\Helper\LinkParametersBuilder;

class GetAllTorrentsExecute {
    
    private $request;
    
    /**
     *
     * @var GetAllTorrents
     */
    private $getAllTorrentsService;
    
    public function __construct(GetAllTorrents $getAllTorrentsService) {
        $this->getAllTorrentsService = $getAllTorrentsService;
    }
    
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
        $this->dispatch();
    }
    
    private function dispatch() {
        $this->setPage();
        $this->setQuery();
    }
    
    private function setPage() {
        $page = $this->request[LinkParametersBuilder::PAGE];
        $this->getAllTorrentsService->setPage($page);
    }
    
    private function setQuery() {
        $query = urlencode($this->request[LinkParametersBuilder::QUERY]);
        $this->getAllTorrentsService->setQuery($query);
    }
    

    public function getAllTorrentsAsArray() {
        return $this->getAllTorrentsService->execute();
        return $this->getAllTorrentsService->getTorrentsAsArray();
    }
    
}

