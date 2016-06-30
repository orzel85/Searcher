<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

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
        if(empty($divInnerContainerList)) {
            return null;
        }
        $divInnerContainer = $divInnerContainerList->item(1);
        if(empty($divInnerContainer)) {
            return null;
        }
        $divInnerContainerChildren = $divInnerContainer->getElementsByTagName('div');
        if(empty($divInnerContainerChildren)) {
            return null;
        }
        $length = $divInnerContainerChildren->length;
        $torrentsList = array();
        for($i = 4 ; $i < $length-3 ; $i++) {
            $torrent = new Torrents();
            $element = $divInnerContainerChildren->item($i);
            if(!empty($element)) {
                $this->parseSingleDiv($element, $torrent);
                $this->addToTorrentList($torrent);
            }
        }
        return $this->torrentList;
    }
    
    private function parseSingleDiv(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setProvider(Controller::TORRENTDOWNLOADS);
        $aList = $domNode->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $aList->item(0);
        if(empty($link)) {
            $this->dontAddToTorrentList();
            return;
        }
        $hrefNode = $link->getAttributeNode('href');
        if(empty($hrefNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $hrefNode->value;
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        if(strpos($href, 'www.torrentdownload.ws') != false) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = trim($link->nodeValue);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $spanList = $domNode->getElementsByTagName('span');
        if(empty($spanList)) {
            $seeds = 0;
            $peers = 0;
        }
        $seedsNode = $spanList->item(2);
        if(!empty($seedsNode)) {
            $seeds = $seedsNode->nodeValue;
        }
        $peersNode = $spanList->item(1);
        if(!empty($peersNode)) {
            $peers = $peersNode->nodeValue;
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        $sizeNode = $spanList->item(3);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setSizeOriginal($domNode->nodeValue);
        $length = strlen($domNode->nodeValue);
        $sizeString = substr($domNode->nodeValue, 0, $length-4);
        if(empty($sizeString)) {
            return;
        }
        if(empty($domNode->nodeValue[$length-2]) || empty($domNode->nodeValue[$length-1])) {
            return;
        }
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