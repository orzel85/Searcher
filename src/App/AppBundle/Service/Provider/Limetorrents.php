<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

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
        $resultsNode = $resultsListNode[1];
        $resultsList = $resultsNode->getElementsByTagName('tr');
        $length = $resultsList->length;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList[$i], $torrent);
            $torrentsList[] = $torrent;
        }
        return $torrentsList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $hrefNode = $this->getElementByTagAndIndex($domNode, 'a', 1);
        $href = $this->getHrefAttributeFromHyperlinkNode($hrefNode);
        $name = $this->getValueFromHyperlinkNode($hrefNode);
        $link = $this->providerUrl . $href;
        $tdList = $domNode->getElementsByTagName('td');
        $torrent->setName($name);
        $torrent->setLink($link);
        $seeds = $tdList[3]->nodeValue;
        $peers = $tdList[4]->nodeValue;
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::LIMETORRENTS);
        $this->setSize($tdList[2],  $torrent);
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