<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class Extratorrent extends Template {
    
    protected $searchUrl = 'http://extratorrent.cc/search/';
    
    protected $providerUrl = 'http://extratorrent.cc';


    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = file_get_contents($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="tl"]');
        $resultsNode = $resultsListNode[0];
        $resultsList = $resultsNode->getElementsByTagName('tr');
        $length = $resultsList->length;
//        var_dump($length);
//        die();
        $torrentsList = array();
        for($i = 2 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList[$i], $torrent);
            $torrentsList[] = $torrent;
        }
//        var_dump($torrentsList);
        return $torrentsList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $aList = $domNode->getElementsByTagName('a');
        $link = $aList[$aList->length - 2];
        $href = $this->getHrefAttributeFromHyperlinkNode($link);
        $name = $this->getValueFromHyperlinkNode($link);
        $tdList = $domNode->getElementsByTagName('td');
        $seeds = 0;
        $peers = 0;
        foreach($tdList as $td) {
            if($td->hasAttribute('class')) {
                $tdClassAttr = $td->getAttributeNode('class');
                if($tdClassAttr->value == 'sy') {
                    $seeds = $td->nodeValue;
                }
                if($tdClassAttr->value == 'ly') {
                    $peers = $td->nodeValue;
                }
            }
        }
        $link = $this->providerUrl . $href;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::EXTRATORRENT);
        $this->setSize($tdList[3],  $torrent);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $size = $this->explodeSizeByNbsp($domNode->nodeValue);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($size->value. ' '. $size->type);
        $torrent->setSize($sizeToDb);
    }

    
    
    private function createUrl() {
        return $this->searchUrl . '?page='. $this->page .'&search=' . urlencode($this->query) . '&s_cat=&srt=added&pp=50&order=desc';
    }
}