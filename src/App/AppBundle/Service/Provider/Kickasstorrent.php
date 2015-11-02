<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

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
        $resultsNode = $resultsListNode[0];
        $resultsList = $resultsNode->getElementsByTagName('tr');
        $length = $resultsList->length;
        $torrentsList = array();
        for($i = 2 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList[$i], $torrent);
            $this->addToTorrentList($torrent);
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $link = $this->getFirstElementByTagAndAttribute($domNode, 'a', 'class', 'cellMainLink');
        $href = $this->getHrefAttributeFromHyperlinkNode($link);
        $name = $this->getValueFromHyperlinkNode($link);
        $tdList = $domNode->getElementsByTagName('td');
        $seeds = $tdList[4]->nodeValue;
        $peers = $tdList[5]->nodeValue;
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::KICKASSTORRENT);
        $this->setSize($tdList[1],  $torrent);
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