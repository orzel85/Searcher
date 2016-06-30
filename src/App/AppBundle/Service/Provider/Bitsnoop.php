<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

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
        if(empty($resultsListNode)) {
            return null;
        }
        $resultsNode = $resultsListNode->item(0);
        if(empty($resultsNode)) {
            return null;
        }
        $resultsList = $resultsNode->getElementsByTagName('li');
        if(empty($resultsList)) {
            return null;
        }
        $length = $resultsList->length;
        $torrentsList = array();
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
        $torrent->setProvider(Controller::BITSNOOP);
        $aList = $domNode->getElementsByTagName('a');
        if(empty($aList)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $aList->item(0);
        if(empty($link)) {
            $this->dontAddToTorrentList();
            return;
        }
        $hrefNode = $link->getAttributeNode('href');
        if(empty($hrefNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $hrefNode->value;
        $name = trim($link->nodeValue);
        $spanList = $domNode->getElementsByTagName('span');
        $torrent->setName($name);
        $link = $this->providerUrl . $href;
        $torrent->setLink($link);
        $seeds = 0;
        $peers = 0;
        if(empty($spanList)) {
            $torrent->setPeers($peers);
            $torrent->setSeeds($seeds);
        }
        foreach($spanList as $span) {
            if($span->hasAttribute('class')) {
                $spanClassAttr = $span->getAttributeNode('class');
                if(empty($spanClassAttr)) {
                    break;
                }
                if($spanClassAttr->value == 'seeders') {
                    $seeds = $span->nodeValue;
                }
                if($spanClassAttr->value == 'leechers') {
                    $peers = $span->nodeValue;
                }
            }
        }
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $this->setSize($domNode,  $torrent);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeType = Size::SIZE_TYPE_MB;
        $size = 0;
        $numberString = 0;
        $torrent->setSizeOriginal($numberString . ' '. $sizeType);
        $torrent->setSize($size);
        $divList = $domNode->getElementsByTagName('div');
        if(empty($divList)) {
            return;
        }
        $div1 = $divList->item(0);
        if(empty($div1)) {
            return;
        }
        $tdList = $div1->getElementsByTagName('td');
        if(empty($tdList)) {
            return;
        }
        $td1 = $tdList->item(0);
        if(empty($td1)) {
            return;
        }
        $value = $td1->nodeValue;
        if(empty($value)) {
            return;
        }
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