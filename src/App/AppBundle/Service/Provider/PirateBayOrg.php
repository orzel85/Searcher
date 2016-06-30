<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

class PirateBayOrg extends Template {
    
    protected $searchUrl = 'https://thepiratebay.se/search/';
    
    protected $providerUrl = 'https://thepiratebay.se';


    public function getTorrentList() {
        $url = $this->createUrl();
        $curlFlags = array(
            CURLOPT_FOLLOWLOCATION => true,
        );
        $pageContent = $this->getPageContent($url, array(),$curlFlags);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@id="searchResult"]');
        if(empty($resultsListNode)) {
            throw new \Exception('Empty. ' . __FILE__ . '::' . __LINE__, 1);
        }
        $resultsNode = $resultsListNode->item(0);
        if(empty($resultsNode)) {
            throw new \Exception('Empty. ' . __FILE__ . '::' . __LINE__, 1);
        }
        $trList = $resultsNode->getElementsByTagName('tr');
        if(empty($trList)) {
            return null;
        }
        $length = $trList->length;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $el = $trList->item($i);
            if(empty($el)) {
                continue;
            }
            $this->parseSinglElement($el, $torrent);
            $this->addToTorrentList($torrent);
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $torrent->setProvider(Controller::PIRATEBAYORG);
        $tdList = $domNode->getElementsByTagName('td');
        if(empty($tdList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $seedsTd = $tdList->item(2);
        $peersTd = $tdList->item(3);
        $seeds = 0;
        $peers = 0;
        if(!empty($seedsTd)) {
            $seeds = $seedsTd->nodeValue;
        }
        if(!empty($peersTd)) {
            $peers = $peersTd->nodeValue;
        }
        $this->setName($tdList->item(1), $torrent);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        if(!empty($domNode)) {
            $this->setSize($domNode,  $torrent);
        }
    }
    
    private function setName(\DOMElement $node, Torrents $torrent) {
        $aList = $node->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $aNode = $aList->item(0);
        if(empty($aNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $this->getValueFromHyperlinkNode($aNode);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        
        $restUrl = $this->getHrefAttributeFromHyperlinkNode($aNode);
        if(empty($restUrl)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $this->providerUrl . $restUrl;
        $torrent->setName($name);
        $torrent->setLink($link);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $fontList = $domNode->getElementsByTagName('font');
        if(empty($fontList)) {
            return;
        }
        $fontNode = $fontList->item(0);
        if(empty($fontNode)) {
            return;
        }
        $textArray = explode(',', $fontNode->nodeValue);
        if(empty($textArray[1])) {
            return;
        }
        $sizeText = trim($textArray[1]);
        $value = trim(str_replace(array('Size', 'i'), array('',''), $sizeText));
        $length = strlen($value);
        if(empty($value[$length - 2]) || empty($value[$length - 1])) {
            return;
        }
        $sizeType = $value[$length - 2] . $value[$length - 1];
        $sizeValue = substr($value, 0, $length-4);
        if(empty($sizeValue)) {
            return;
        }
        if(empty($sizeType)) {
            return;
        }
        $torrent->setSizeOriginal($sizeValue . ' '. $sizeType);
        $torrent->setSize($this->parseSize($sizeValue, $sizeType));
    }

    
    
    private function createUrl() {
        $page = $this->page - 1;
        $query = str_replace(' ', '%20', $this->query);
        return $this->searchUrl . $query . '/'. $page .'/99/0/';
    }
}