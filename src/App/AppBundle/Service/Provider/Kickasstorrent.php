<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Kickasstorrent extends Template {
    
    protected $searchUrl = 'https://kat.cr/usearch/';
    
    protected $providerUrl = 'https://kat.cr';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = $this->getPageContent($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@id="mainSearchTable"]');
        if(empty($resultsListNode)) {
            return null;
        }
        $resultsNode = $resultsListNode->item(0);
        if(empty($resultsNode)) {
            return null;
        }
        $resultsList = $resultsNode->getElementsByTagName('tr');
        if(empty($resultsList)) {
            return null;
        }
        $length = $resultsList->length;
        for($i = 2 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $element = $resultsList->item($i);
            if(!empty($element)) {
                $this->parseSinglElement($element, $torrent);
                $this->addToTorrentList($torrent);
            }
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setProvider(Controller::KICKASSTORRENT);
        $link = $this->getFirstElementByTagAndAttribute($domNode, 'a', 'class', 'cellMainLink');
        if(empty($link)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($link);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $this->getValueFromHyperlinkNode($link);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $tdList = $domNode->getElementsByTagName('td');
        if(empty($tdList)) {
            $this->setDefaultsOnTorrent();
            return;
        }
        $torrent->setName($name);
        $link = $this->providerUrl . $href;
        $torrent->setLink($link);
        $seedsTd = $tdList->item(4);
        if(!empty($seedsTd)) {
            $seeds = $seedsTd->nodeValue;
        }else{
            $seeds = 0;
        }
        $peersTd = $tdList->item(5);
        if(!empty($peersTd)) {
            $peers = $peersTd->nodeValue;
        }else{
            $peers = 0;
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        $sizeNode = $tdList->item(1);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }
        
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $size = $this->explodeSizeBySpace($domNode->nodeValue);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($size->value . ' '. $size->type);
        $torrent->setSize($sizeToDb);
    }
    
    private function createUrl() {
        if($this->page == 1) {
            return $this->searchUrl . urlencode($this->query) . '/';
        }else{
            return $this->searchUrl . urlencode($this->query) . '/' . $this->page .'/';
        }
    }
}