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
        $this->torrentsListService->setQueryFromRequest("mr robot s01e05 720p");
        $this->torrentsListService->setPage(1);
        $this->torrentsListService->setProviderCode(ProviderController::TORRENTHOUND);
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