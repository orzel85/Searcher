<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Limetorrents extends Template {
    
    protected $searchUrl = 'https://www.limetorrents.cc/search/all/';
    
    protected $providerUrl = 'https://www.limetorrents.cc';


    public function getTorrentList() {
        $url = $this->createUrl();
        $curlFlags = array(
            CURLOPT_FOLLOWLOCATION => true,
        );
        $pageContent = $this->getPageContent($url, array(), $curlFlags);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="table2"]');
        if(empty($resultsListNode)) {
            return null;
        }
        $resultsNode = $resultsListNode->item(1);
        if(empty($resultsNode)) {
            return null;
        }
        $resultsList = $resultsNode->getElementsByTagName('tr');
        if(empty($resultsList)) {
            return null;
        }
        $length = $resultsList->length;
        for($i = 1 ; $i < $length ; $i++) {
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
        $torrent->setProvider(Controller::LIMETORRENTS);
        $hrefNode = $this->getElementByTagAndIndex($domNode, 'a', 1);
        if(empty($hrefNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($hrefNode);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $this->getValueFromHyperlinkNode($hrefNode);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $this->providerUrl . $href;
        $tdList = $domNode->getElementsByTagName('td');
        $torrent->setName($name);
        $torrent->setLink($link);
        if(empty($tdList)) {
            $this->setDefaultsOnTorrent($torrent);
            return;
        }
        $seedsTd = $tdList->item(3);
        if(!empty($seedsTd)) {
            $seeds = $seedsTd->nodeValue;
        }else{
            $seeds = 0;
        }
        $peersTd = $tdList->item(4);
        if(!empty($peersTd)) {
            $peers = $peersTd->nodeValue;
        }else{
            $peers = 0;
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        $sizeNode = $tdList->item(2);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeOriginal = $domNode->nodeValue;
        $size = $this->explodeSizeBySpace($sizeOriginal);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($sizeOriginal);
        $torrent->setSize($sizeToDb);
    }

    private function spaceToDash($arg) {
        return str_replace(' ', '-', $arg);
    }
    
    private function createUrl() {
        return $this->searchUrl . $this->spaceToDash($this->query) .'/date/'. $this->page;
    }
}