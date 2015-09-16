<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;

class Isohunt extends Template {
    
    protected $searchUrl = 'https://isohunt.to/torrents/?ihq=';
    
    protected $providerUrl = 'https://isohunt.to';


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
        $resultsListNode = $xpath->query('//table[@class="table-torrents table table-striped table-hover"]');
        $resultsNode = $resultsListNode[0];
        $resultsList = $resultsNode->getElementsByTagName('tr');
        $length = $resultsList->length - 1;
        $torrentsList = array();
        for($i = 1 ; $i < $length ; $i++) {
            $torrent = new Torrents();
            $this->parseSinglElement($resultsList[$i], $torrent);
            $torrentsList[] = $torrent;
        }
        return $torrentsList;
    }
    
    private function parseSinglElement(\DOMElement $domNode, Torrents $torrent) {
        $hrefNode = $this->getElementByTagAndIndex($domNode, 'a', 0);
        $href = $this->getHrefAttributeFromHyperlinkNode($hrefNode);
        $name = $this->getValueFromHyperlinkNode($hrefNode);
        $link = $this->providerUrl . $href;
        $tdList = $domNode->getElementsByTagName('td');
        $torrent->setName($name);
        $torrent->setLink($link);
        $seeds = $tdList[6]->nodeValue;
        $peers = 0;
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $torrent->setProvider(Controller::ISOHUNT);
        $this->setSize($tdList[5],  $torrent);
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeOriginal = $domNode->nodeValue;
        $size = $this->explodeSizeBySpace($sizeOriginal);
        $sizeToDb = $this->parseSize($size->value, $size->type);
        $torrent->setSizeOriginal($sizeOriginal);
        $torrent->setSize($sizeToDb);
    }

    private function createUrl() {
        $limit = ($this->page - 1) * 40;
        return $this->searchUrl . urlencode($this->query) .'&Torrent_page='. $limit;
    }
}