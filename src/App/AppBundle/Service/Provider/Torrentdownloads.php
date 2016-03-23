<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class Torrentdownloads extends Template {
    
    protected $searchUrl = 'http://www.torrentdownloads.me/search/';
    
    protected $providerUrl = 'http://www.torrentdownloads.me';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = $this->getPageContent($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $divInnerContainerList = $xpath->query('//div[@class="inner_container"]');
        $divInnerContainer = $divInnerContainerList->item(1);
        $divInnerContainerChildren = $divInnerContainer->getElementsByTagName('div');
        $length = $divInnerContainerChildren->length;
        $torrentsList = array();
        for($i = 4 ; $i < $length-3 ; $i++) {
            $torrent = new Torrents();
            $this->parseSingleDiv($divInnerContainerChildren->item($i), $torrent);
            $this->addToTorrentList($torrent);
        }
        return $this->torrentList;
    }
    
    private function parseSingleDiv(\DOMElement $domNode, Torrents $torrent) {
        $aList = $domNode->getElementsByTagName('a');
        $link = $aList->item(0);
        
        $hrefNode = $link->getAttributeNode('href');
        $href = $hrefNode->value;
        if(strpos($href, 'www.torrentdownload.ws') != false) {
            $this->dontAddToTorrentList();
        }
        $name = trim($link->nodeValue);
        $spanList = $domNode->getElementsByTagName('span');
        $seedsNode = $spanList->item(2);
        $seeds = $seedsNode->nodeValue;
        $peersNode = $spanList->item(1);
        $peers = $peersNode->nodeValue;
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::TORRENTDOWNLOADS);
        $this->setSize($spanList->item(3), $torrent);
        return true;
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setSizeOriginal($domNode->nodeValue);
        $length = strlen($domNode->nodeValue);
        $sizeString = substr($domNode->nodeValue, 0, $length-4);
        $sizeType = $domNode->nodeValue[$length-2] . $domNode->nodeValue[$length-1];
        $sizeNumber = $this->parseSize($sizeString, $sizeType);
        $torrent->setSize($sizeNumber);
    }

    private function createUrl() {
        if($this->page == 1) {
            return $this->searchUrl . '?search=' . urlencode($this->query);
        }else{
            return $this->searchUrl . '?search=' . urlencode($this->query) . '&page='. $this->page;
        }
    }
}