<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

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
        $resultsNode = $resultsListNode->item(0);
        $trList = $resultsNode->getElementsByTagName('tr');
        $length = $trList->length;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            if(!empty($trList->item($i))) {
                $torrent = new Torrents();
                $this->parseSinglElement($trList->item($i), $torrent);
                $this->addToTorrentList($torrent);
            }
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $aList = $domNode->getElementsByTagName('a');
        $linkNode = $aList->item(0);
        if(empty($linkNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($linkNode);
        if(is_int(strpos($href, 'http:'))) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = trim($linkNode->nodeValue);
        $tdList = $domNode->getElementsByTagName('td');
        $seeds = $tdList->item(4)->nodeValue;
        $peers = $tdList->item(5)->nodeValue;
        $this->setSize($tdList->item(3), $torrent);
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::TORRENTREACTOR);
        return true;
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeArray = explode(' ', $domNode->nodeValue);
        $numberString = $sizeArray[0];
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