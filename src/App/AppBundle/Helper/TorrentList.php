<?php

namespace App\AppBundle\Helper;

class TorrentList {
    
    private $list;
    
    public function getListAsArray() {
        $return = array();
        $row = array();
        /**
         * @var $torrent App\AppBundle\Entity\Torrents
         */
        foreach($this->list as $torrent) {
            $row = array(
                'name' => $torrent->getName(),
                'link' => $torrent->getLink(),
                'linkSha1' => $torrent->getLinkSha1(),
                'provider' => $torrent->getProvider(),
                'seeds' => $torrent->getSeeds(),
                'peers' => $torrent->getPeers(),
                'size' => $torrent->getSize(),
                'sizeOriginal' => $torrent->getSizeOriginal(),
                'createDate' => $this->parseDate($torrent->getCreateDate()),
                'updateDate' => $this->parseDate($torrent->getUpdateDate()),
            );
            $return[] = $row;
        }
        return $return;
    }
    
    private function parseDate($date) {
        if(!is_object($date)) {
            return $date;
        }
        return $date->format("Y-m-d H:i:s");
    }
    
    public function getList() {
        return $this->list;
    }

    public function setList($list) {
        $this->list = $list;
    }

}