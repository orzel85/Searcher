<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

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
        $searchTable = $searchTableList[1];
        if(!is_object($searchTable)) {
            return null;
        }
        $trList = $searchTable->childNodes;
        $length = $trList->length;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSingleTr($trList[$i], $torrent);
            $torrentsList[] = $torrent;
        }
        return $torrentsList;
    }
    
    private function parseSingleTr(\DOMElement $domNode, Torrents $torrent) {
        $tdList = $domNode->getElementsByTagName('td');
        $td1 = $tdList[0];
        $aList = $td1->getElementsByTagName('a');
        $a = $aList[2];
        $name = $a->nodeValue;
        $hrefNode = $a->getAttributeNode('href');
        $href = $hrefNode->value;
        $tdSize = $tdList[2];
        $tdSeeds = $tdList[3];
        $spanSeedsList = $tdSeeds->getElementsByTagName('span');
        $spanSeeds = $spanSeedsList[0];
        $seeds = $this->cleanSeedsLeeches($spanSeeds->nodeValue);
        $tdLeeches = $tdList[4];
        $spanLeechesList = $tdLeeches->getElementsByTagName('span');
        $spanLeeches = $spanLeechesList[0];
        $peers = $this->cleanSeedsLeeches($spanLeeches->nodeValue);
        $link = $this->providerUrl . $href;
        $this->setSize($tdSize, $torrent);
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::TORRENTHOUND);
    }
    
    private function setSize(\DomElement $domNode, Torrents $torrent) {
        $spanList = $domNode->getElementsByTagName('span');
        $span = $spanList[0];
        $originalSize = $span->nodeValue;
        $sizeArray = explode(' ', $span->nodeValue);
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
