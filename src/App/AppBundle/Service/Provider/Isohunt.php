<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Entity\Torrents;
use App\AppBundle\Model\Size;

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
        $length = $resultsList->length - 1;
        for($i = 1 ; $i < $length ; $i++) {
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
        $torrent->setProvider(Controller::ISOHUNT);
        $hrefNode = $this->getElementByTagAndIndex($domNode, 'a', 0);
        if(empty($hrefNode)) {
            $this->dontAddToTorrentList();
            return;
        }
        $href = $this->getHrefAttributeFromHyperlinkNode($hrefNode);
        if(empty($href)) {
            $this->dontAddToTorrentList();
            return;
        }
        $name = $this->getValueFromHyperlinkNode($hrefNode);
        if(empty($name)) {
            $this->dontAddToTorrentList();
            return;
        }
        $link = $this->providerUrl . $href;
        $tdList = $domNode->getElementsByTagName('td');
        $torrent->setName($name);
        $torrent->setLink($link);
        if(empty($tdList)) {
            return;
        }
        $element = $tdList->item(6);
        if(empty($element)) {
            $seeds = 0;
        }
        $seeds = $element->nodeValue;
        $peers = 0;
        $torrent->setPeers($peers);
        $torrent->setSeeds($seeds);
        $sizeNode = $tdList->item(5);
        $torrent->setSizeOriginal('0 '. Size::SIZE_TYPE_MB);
        $torrent->setSize(0);
        if(!empty($sizeNode)) {
            $this->setSize($sizeNode,  $torrent);
        }
    }
    
    private function setSize(\DOMElement $domNode, Torrents $torrent) {
        $sizeOriginal = $domNode->nodeValue;
        if(empty($sizeOriginal)) {
            return;
        }
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