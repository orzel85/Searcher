<?php

namespace App\AppBundle\Service;

use App\AppBundle\Service\Provider\Controller as ProviderController;

class TorrentsListExecute {
    
    private $request;
    
    /**
     *
     * @var TorrentsList
     */
    private $torrentsListService;
    
    public function __construct(TorrentsList $torrentsList) {
        $this->torrentsListService = $torrentsList;
    }
    
    public function getRequest() {
        return $this->request;
    }

    public function setRequest($request) {
        $this->request = $request;
        $this->dispatchParameters();
    }
    
    private function dispatchParameters() {
        $this->torrentsListService->setQueryFromRequest($this->request['query']);
        $this->torrentsListService->setPage($this->request['page']);
        $this->torrentsListService->setProviderCode($this->request['provider']);
    }
    
    public function getTorrentsList() {
        $this->torrentsListService->execute();
        return $this->torrentsListService->getTorrentsList();
    }
    
    public function getTorrentsListAsArray() {
        $this->torrentsListService->execute();
        return $this->torrentsListService->getTorrentsListAsArray();
    }
    
}