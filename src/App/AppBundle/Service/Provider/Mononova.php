<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class Mononova extends Template {
    
    protected $searchUrl = 'https://www.monova.org/search?term=';
    
    protected $providerUrl = 'https://www.monova.org';

    public function getTorrentList() {
        $url = $this->createUrl();
        $pageContent = $this->getPageContent($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($pageContent);
        $xpath = new \DOMXpath($doc);
        $resultsListNode = $xpath->query('//table[@class="table table-bordered main-table"]');
        $resultsNode = $resultsListNode[0];
        $resultsList = $resultsNode->getElementsByTagName('tr');
        $length = $resultsList->length;
        $torrentsList = array();
        for($i = 0 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList[$i], $torrent);
            $this->addToTorrentList($torrent);
        }
        return $this->torrentList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $tdList = $domNode->getElementsByTagName('td');
        $aNode = $this->getElementByTagAndIndex($tdList[0], 'a', 0);
        $href = $this->getHrefAttributeFromHyperlinkNode($aNode);
        $name = $this->getValueFromHyperlinkNode($aNode);
        $link = 'https:' . $href;
        $seeds = $tdList[6]->nodeValue;
        $peers = $tdList[7]->nodeValue;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::MONONOVA);
        $this->setSize($tdList[4],  $torrent);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeOriginal = $domNode->nodeValue;
        $size = $this->explodeSizeBySpace($sizeOriginal);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($sizeOriginal);
        $torrent->setSize($sizeToDb);
    }

    
    
    private function createUrl() {
        return $this->searchUrl . urlencode($this->query) .'&page='. $this->page;
    }
}