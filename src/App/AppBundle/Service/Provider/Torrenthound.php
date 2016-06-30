<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Torrenthound extends Template {
    
    /**
     * http://www.torrenthound.com/search/{query}
     * http://www.torrenthound.com/search/{page}/{query}
     * @var type 
     */
    protected $searchUrl = 'http://www.torrenthound.com/search/';
    
    protected $providerUrl = 'http://www.torrenthound.com';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = $this->getPageContent($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $searchTableList = $xpath->query('//table[@class="searchtable"]');
        if(empty($searchTableList)) {
            return null;
        }
        $searchTable = $searchTableList->item(1);
        if(!is_object($searchTable)) {
            return null;
        }
        if(empty($searchTable)) {
            return null;
        }
        $trList = $searchTable->childNodes;
        if(empty($trList)) {
            return null;
        }
        $length = $trList->length;
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $element = $trList->item($i);
            if(!empty($element)) {
                $this->parseSingleTr($element, $torrent);
                $this->addToTorrentList($torrent);
            }
        }
        return $this->torrentList;
    }
    
    private function parseSingleTr(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setProvider(Controller::TORRENTHOUND);
        $tdList = $domNode->getElementsByTagName('td');
        if(empty($tdList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $td1 = $tdList->item(0);
        if(empty($td1)) {
            $this->dontAddToTorrentList();
            return;
        }
        $aList = $td1->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $a = $aList->item(2);
        if(empty($a)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $a->nodeValue;
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $hrefNode = $a->getAttributeNode('href');
        if(empty($hrefNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $hrefNode->value;
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        $tdSize = $tdList->item(2);
        if(!empty($tdSize)) {
            $this->setSize($tdSize, $torrent);
        }
        $seeds = 0;
        $peers = 0;
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $tdSeeds = $tdList->item(3);
        if(empty($tdSeeds)) {
            return;
        }
        $spanSeedsList = $tdSeeds->getElementsByTagName('span');
        if(empty($spanSeedsList)) {
            return;
        }
        $spanSeeds = $spanSeedsList->item(0);
        if(empty($spanSeeds)) {
            return;
        }
        $seeds = $this->cleanSeedsLeeches($spanSeeds->nodeValue);
        if(empty($seeds)) {
            return;
        }
        $tdLeeches = $tdList->item(4);
        if(empty($tdLeeches)) {
            return;
        }
        $spanLeechesList = $tdLeeches->getElementsByTagName('span');
        if(empty($spanLeechesList)) {
            return;
        }
        $spanLeeches = $spanLeechesList->item(0);
        if(empty($spanLeeches)) {
            return;
        }
        $peers = $this->cleanSeedsLeeches($spanLeeches->nodeValue);
        if(empty($peers)) {
            return;
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
    }
    
    private function setSize(\DomElement $domNode, Torrents $torrent) {
        $spanList = $domNode->getElementsByTagName('span');
        if(empty($spanList)) {
            return;
        }
        $span = $spanList->item(0);
        if(empty($span)) {
            return;
        }
        $originalSize = $span->nodeValue;
        if(empty($originalSize)) {
            return;
        }
        $sizeArray = explode(' ', $span->nodeValue);
        if(empty($sizeArray)) {
            return;
        }if(empty($sizeArray[0] || empty($sizeArray[1]))) {
            return;
        }
        $sizeType = strtoupper($sizeArray[1]);
        $sizeString = str_replace(',', '', $sizeArray[0]);
        $size = $this->parseSize($sizeString, $sizeType);
        $torrent->setSize($size);
        $torrent->setSizeOriginal($originalSize);
    }
    
    private function cleanSeedsLeeches($arg) {
        $return = '';
        for($i=0 ; $i < strlen($arg) ; $i++) {
            if(is_numeric($arg[$i])) {
                $return .= $arg[$i];
            }
        }
        return (int)$return;
    }
    
    private function createUrl() {
        if($this->page == 1) {
            return $this->searchUrl . urlencode($this->query);
        }else{
            return $this->searchUrl . $this->page .'/'. urlencode($this->query);
        }
    }

    
    
}
