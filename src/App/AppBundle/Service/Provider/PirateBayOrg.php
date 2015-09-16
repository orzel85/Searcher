<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class PirateBayOrg extends Template {
    
    protected $searchUrl = 'https://thepiratebay.la/search/';
    
    protected $providerUrl = 'https://thepiratebay.la';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = $this->getPageContent($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@id="searchResult"]');
        $resultsNode = $resultsListNode[0];
        $trList = $resultsNode->getElementsByTagName('tr');
        $length = $trList->length;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($trList[$i], $torrent);
            $torrentsList[] = $torrent;
        }
        return $torrentsList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $tdList = $domNode->getElementsByTagName('td');
        $seeds = $tdList[2]->nodeValue;
        $peers = $tdList[3]->nodeValue;
        $this->setName($tdList[1], $torrent);
        $this->setSize($tdList[1], $torrent);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::PIRATEBAYORG);
        $this->setSize($domNode,  $torrent);
    }
    
    private function setName(\DOMElement $node, Torrents $torrent) {
        $aList = $node->getElementsByTagName('a');
        $aNode = $aList[0];
        $name = $this->getValueFromHyperlinkNode($aNode);
        $link = $this->providerUrl . $this->getHrefAttributeFromHyperlinkNode($aNode);
        $torrent->setName($name);
        $torrent->setLink($link);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $fontList = $domNode->getElementsByTagName('font');
        $fontNode = $fontList[0];
        $textArray = explode(',', $fontNode->nodeValue);
        $sizeText = trim($textArray[1]);
        $value = trim(str_replace(array('Size', 'i'), array('',''), $sizeText));
        $length = strlen($value);
        $sizeType = $value[$length - 2] . $value[$length - 1];
        $sizeValue = substr($value, 0, $length-4);
        $torrent->setSizeOriginal($sizeValue . ' '. $sizeType);
        $torrent->setSize($this->parseSize($sizeValue, $sizeType));
    }

    
    
    private function createUrl() {
        $page = $this->page - 1;
        $query = str_replace(' ', '%20', $this->query);
        return $this->searchUrl . $query . '/'. $page .'/99/0/';
    }
}