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
        $this->setPage();
        $this->setProvider();
        $this->setQuery();
    }
    
    private function setPage() {
        $page = $this->request['page'];
        $this->torrentsListService->setPage($page);
    }
    
    private function setProvider() {
        $provider = $this->request['provider'];
        $this->torrentsListService->setProviderCode($provider);
    }
    
    private function setQuery() {
        $query = $this->request['query'];
        $this->torrentsListService->setQueryFromRequest($query);
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