<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Extratorrent extends Template {
    
    protected $searchUrl = 'http://extratorrent.cc/search/';
    
    protected $providerUrl = 'http://extratorrent.cc';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = file_get_contents($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="tl"]');
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
        $torrent->setProvider(Controller::EXTRATORRENT);
        $aList = $domNode->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $aList->item($aList->length - 2);
        if(empty($link)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($link);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        if(strpos($href, 'extratorrent.cc') != false) {
            $this->dontAddToTorrentList();
        }
        $name = $this->getValueFromHyperlinkNode($link);
        $tdList = $domNode->getElementsByTagName('td');
        $torrent->setName($name);
        $link = $this->providerUrl . $href;
        $torrent->setLink($link);
        if(empty($tdList)) {
            $this->setDefaultsOnTorrent($torrent);
            return;
        }
        $seeds = 0;
        $peers = 0;
        foreach($tdList as $td) {
            if($td->hasAttribute('class')) {
                $tdClassAttr = $td->getAttributeNode('class');
                if($tdClassAttr->value == 'sy') {
                    $seeds = $td->nodeValue;
                }
                if($tdClassAttr->value == 'ly') {
                    $peers = $td->nodeValue;
                }
            }
        }
        
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $sizeNode = $tdList->item(3);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }else{
            $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
            $torrent->setSize(0);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        if(empty($domNode->nodeValue)) {
            return;
        }
        $size = $this->explodeSizeByNbsp($domNode->nodeValue);
        if(empty($size)) {
            return;
        }
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($size->value. ' '. $size->type);
        $torrent->setSize($sizeToDb);
    }

    
    
    private function createUrl() {
        return $this->searchUrl . '?page='. $this->page .'&search=' . urlencode($this->query) . '&s_cat=&srt=added&pp=50&order=desc';
    }
}