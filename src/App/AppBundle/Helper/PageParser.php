<?php

namespace App\AppBundle\Helper;

class PageParser {
    
    /**
     * 
     * @param \DOMElement $domElement
     * @param string $tagName
     * @param string $attributeName
     * @param string $attributeValue
     * @return DOMElement
     */
    public static function getFirstElementByTagAndAttribute(\DOMElement $domElement, $tagName, $attributeName, $attributeValue) {
        $list = $domElement->getElementsByTagName($tagName);
        foreach($list as $element) {
             if($element->hasAttribute($attributeName)) {
                $attribute = $element->getAttributeNode($attributeName);
                if(strpos($attribute->value, $attributeValue) !== false) {
                    return $element;
                }
             }
        }
    }
    
    /**
     * Getting element by it's tag name and index from other \DomElement
     * 
     * @param \DOMElement $domElement
     * @param string $tagName
     * @param integer $index
     * @return \DOMElement
     */
    public static function getElementByTagAndIndex(\DOMElement $domElement, $tagName, $index) {
        $list = $domElement->getElementsByTagName($tagName);
        return $list->item($index);
    }
    
}