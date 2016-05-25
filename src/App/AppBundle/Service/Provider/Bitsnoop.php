<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class Bitsnoop extends Template {
    
    protected $searchUrl = 'http://bitsnoop.com/search/all/';
    
    protected $providerUrl = 'http://bitsnoop.com';


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
        $resultsListNode = $xpath->query('//ol[@id="torrents"]');
        $resultsNode = $resultsListNode->item(0);
        $resultsList = $resultsNode->getElementsByTagName('li');
        $length = $resultsList->length;
        $torrentsList = array();
        for($i = 0 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList->item($i), $torrent);
            $this->addToTorrentList($torrent);
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $aList = $domNode->getElementsByTagName('a');
        $link = $aList->item(0);
        $hrefNode = $link->getAttributeNode('href');
        $href = $hrefNode->value;
        $name = trim($link->nodeValue);
        $spanList = $domNode->getElementsByTagName('span');
        $seeds = 0;
        $peers = 0;
        foreach($spanList as $span) {
            if($span->hasAttribute('class')) {
                $spanClassAttr = $span->getAttributeNode('class');
                if($spanClassAttr->value == 'seeders') {
                    $seeds = $span->nodeValue;
                }
                if($spanClassAttr->value == 'leechers') {
                    $peers = $span->nodeValue;
                }
            }
        }
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::BITSNOOP);
        $this->setSize($domNode,  $torrent);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $divList = $domNode->getElementsByTagName('div');
        $div1 = $divList->item(0);
        $tdList = $div1->getElementsByTagName('td');
        $td1 = $tdList->item(0);
        $value = $td1->nodeValue;
        $length = strlen($value);
        $numberString = '';
        for($i = 0 ; $i < $length ; $i++) {
            if($value[$i] != ' ') {
                $numberString .= $value[$i];
            }else{
                break;
            }
        }
        $numberString = str_replace(',', '.', $numberString);
        $sizeType = $value[$i+1] . $value[$i+2];
        $size = $this->parseSize($numberString, $sizeType);
        $torrent->setSizeOriginal($numberString . ' '. $sizeType);
        $torrent->setSize($size);
    }

    
    
    private function createUrl() {
        return $this->searchUrl . urlencode($this->query) .'/c/d/'. $this->page .'/';
    }
}