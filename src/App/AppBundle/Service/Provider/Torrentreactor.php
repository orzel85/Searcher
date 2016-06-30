<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Torrentreactor extends Template {
    
    protected $searchUrl = 'http://torrentreactor.com/torrents-search/';
    
    protected $providerUrl = 'http://torrentreactor.com/';


    public function getTorrentList() {
        $url = $this->createUrl();
        $array = array();
        $flags = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTREDIR => 3,
        );
        $pageContent = $this->getPageContent($url, $array, $flags);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="col-sm-12"]');
        if(empty($resultsListNode)) {
            return null;
        }
        $resultsNode = $resultsListNode->item(0);
        if(empty($resultsNode)) {
            return null;
        }
        $trList = $resultsNode->getElementsByTagName('tr');
        if(empty($trList)) {
            return null;
        }
        $length = $trList->length;
        if(empty($length)) {
            return null;
        }
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $element = $trList->item($i);
            if(!empty($element)) {
                $this->parseSinglElement($element, $torrent);
                $this->addToTorrentList($torrent);
            }
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setProvider(Controller::TORRENTREACTOR);
        $aList = $domNode->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $linkNode = $aList->item(0);
        if(empty($linkNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($linkNode);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        if(is_int(strpos($href, 'http:'))) {
            $this->dontAddToTorrentList();
            return;
        }
        if(empty($linkNode->nodeValue)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = trim($linkNode->nodeValue);
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $tdList = $domNode->getElementsByTagName('td');
        if(empty($tdList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $seeds = 0;
        $peers = 0;
        $seedsNode = $tdList->item(4);
        if(!empty($seedsNode)) {
            $seeds = $seedsNode->nodeValue;
        }
        $peersNode = $tdList->item(5);
        if(!empty($peersNode)) {
            $peers = $peersNode->nodeValue;
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        $element = $tdList->item(3);
        if(!empty($element)) {
            $this->setSize($element, $torrent);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        if(empty($domNode->nodeValue)) {
            return;
        }
        $sizeArray = explode(' ', $domNode->nodeValue);
        if(empty($sizeArray[0])) {
            return;
        }
        $numberString = $sizeArray[0];
        if(empty($sizeArray[1])) {
            return;
        }
        $sizeType = $sizeArray[1];
        $size = $this->parseSize($numberString, $sizeType);
        $torrent->setSizeOriginal($numberString . ' '. $sizeType);
        $torrent->setSize($size);
    }

    
    
    private function createUrl() {
        $limit = ($this->page - 1) * 35;
        return $this->searchUrl . urlencode($this->query) .'/'. $limit;
    }
}