<?php

namespace App\AppBundle\Service;

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
        $page = 1;
        $this->getAllTorrentsService->setPage($page);
    }
    
    private function setQuery() {
        $query = urlencode('harry potter');
        $this->getAllTorrentsService->setQuery($query);
    }
    

    public function getAllTorrentsAsArray() {
        $this->getAllTorrentsService->execute();
        return $this->getAllTorrentsService->getTorrentsAsArray();
    }
    
}

