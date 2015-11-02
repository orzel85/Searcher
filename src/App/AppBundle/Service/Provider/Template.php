<?php

namespace App\AppBundle\Service\Provider;

use App\AppBundle\Service\Curl;
use App\AppBundle\Helper\NumberParser;
use App\AppBundle\Helper\SizeParser;
use App\AppBundle\Helper\PageParser;

abstract class Template {
    
    protected $searchrl;
    
    protected $providerUrl;
    
    protected $page;
    
    protected $query;
    
    protected $torrentList;
    
    protected $addToTorrentList = true;
    
    abstract public function getTorrentList();
    
    protected function getPageContent($url, $postArray = array(), $curlFlags = array()) {
        $curlService = new Curl();
        return $curlService->send($url, $postArray, $curlFlags);
    }
    
    protected function addToTorrentList($torrent) {
        if($this->addToTorrentList) {
            $this->torrentList[] = $torrent;
        }else{
            $this->addToTorrentList = true;
        }
    }
    
    protected function dontAddToTorrentList() {
        $this->addToTorrentList = false;
    }
    
    /**
     * 
     * @param \DOMElement $aNode
     * @return string
     */
    protected function getValueFromHyperlinkNode(\DOMElement $aNode) {
        return trim($aNode->nodeValue);
    }
    
    /**
     * 
     * @param \DOMElement $aNode
     * @return string
     */
    protected function getHrefAttributeFromHyperlinkNode(\DOMElement $aNode) {
        $hrefNode = $aNode->getAttributeNode('href');
        return $hrefNode->value;
    }
    
    /**
     * Getting first occurence of DomElement from other DomElement, getting by tag, attribute name and its value.
     * 
     * @param \DOMElement $domElement
     * @param string $tagName
     * @param string $attributeName
     * @param string $attributeValue
     * @return \DOMElement
     */
    protected function getFirstElementByTagAndAttribute(\DOMElement $domElement, $tagName, $attributeName, $attributeValue) {
        return PageParser::getFirstElementByTagAndAttribute($domElement, $tagName, $attributeName, $attributeValue);
    }
    
    /**
     * Parses given string to integer. String is file size with, KB, MB, GB and 
     * returns value in bytes as integer.
     * 
     * @param type $sizeString
     * @param type $sizeType i.e.: kB, KB, MB, GB
     * 
     * @return integer in MB
     */
    protected function parseSize($sizeString, $sizeType) {
        $sizeStringReplaced = str_replace(',', '.', $sizeString);
        return NumberParser::parseSizeToInteger($sizeStringReplaced, $sizeType);
    }
    
    /**
     * Getting element by it's tag name and index from other \DomElement
     * 
     * @param \DOMElement $domElement
     * @param string $tagName
     * @param integer $index
     * @return \DOMElement
     */
    protected function getElementByTagAndIndex(\DOMElement $domElement, $tagName, $index) {
        return PageParser::getElementByTagAndIndex($domElement, $tagName, $index);
    }
    
        /**
     * Explodes string in format [sizeNumber][space][sizeType] i.e.: 11.6 GB
     * 
     * @param type $stringSize
     * @return Size
     */
    protected function explodeSizeBySpace($sizeString) {
        return SizeParser::explodeSizeBySpace($sizeString);
    }
    
    protected function explodeSizeByNbsp($sizeString) {
        return SizeParser::explodeSizeByNbsp($sizeString);
    }
    
    public function getPage() {
        return $this->page;
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function getQuery() {
        return $this->query;
    }

    public function setQuery($query) {
        $this->query = $query;
    }
    
}