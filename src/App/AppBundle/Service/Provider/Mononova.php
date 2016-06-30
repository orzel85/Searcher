<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class Mononova extends Template {
    
    protected $searchUrl = 'https://www.monova.org/search?term=';
    
    protected $providerUrl = 'https://www.monova.org';

    public function getTorrentList() {
        $url = $this->createUrl();
        $flags = array(
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_POSTREDIR => 3,
        );
        $array = array();
        $pageContent = $this->getPageContent($url, $array, $flags);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="bordered main-table"]');
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
        for($i = 0 ; $i < $length ; $i++) {
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
        $torrent->setProvider(Controller::MONONOVA);
        $tdList = $domNode->getElementsByTagName('td');
        if(empty($tdList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $aNode = $this->getElementByTagAndIndex($tdList->item(1), 'a', 0);
        if(empty($aNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($aNode);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $this->getValueFromHyperlinkNode($aNode);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = 'https:' . $href;
        $torrent->setName($name);
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
        $sizeNode = $tdList->item(3);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeOriginal = $domNode->nodeValue;
        if($sizeOriginal == 'N/A' || empty($sizeOriginal)) {
            $torrent->setSizeOriginal(0);
            $torrent->setSize(0);
            return;
        }
        $size = $this->explodeSizeBySpace($sizeOriginal);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($sizeOriginal);
        $torrent->setSize($sizeToDb);
    }

    
    
    private function createUrl() {
        return $this->searchUrl . urlencode($this->query) .'&page='. $this->page;
    }
}