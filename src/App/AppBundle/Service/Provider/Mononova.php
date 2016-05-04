<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

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
        $resultsNode = $resultsListNode->item(0);
        $resultsList = $resultsNode->getElementsByTagName('tr');
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
        $tdList = $domNode->getElementsByTagName('td');
        $aNode = $this->getElementByTagAndIndex($tdList->item(1), 'a', 0);
        $href = $this->getHrefAttributeFromHyperlinkNode($aNode);
        $name = $this->getValueFromHyperlinkNode($aNode);
        $link = 'https:' . $href;
        $seeds = $tdList->item(4)->nodeValue;
        $peers = $tdList->item(5)->nodeValue;
        $torrent->setName($name);
        $torrent->setLink($link);
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::MONONOVA);
        $this->setSize($tdList->item(3),  $torrent);
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