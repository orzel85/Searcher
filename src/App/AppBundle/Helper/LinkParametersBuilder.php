<?php

namespace App\AppBundle\Helper;

class LinkParametersBuilder {
    
    const PROVIDER = 'provider';
    
    const PAGE = 'page';
    
    const QUERY = 'query';
    
    private $provider;
    
    private $page;
    
    private $query;
    
    public function getProvider() {
        return $this->provider;
    }

    public function getPage() {
        return $this->page;
    }

    public function getQuery() {
        return $this->query;
    }

    public function setProvider($provider) {
        $this->provider = $provider;
    }

    public function setPage($page) {
        $this->page = $page;
    }

    public function setQuery($query) {
        $this->query = $query;
    }

    public function getParamsAsString() {
        $paramsArray[] = $this->getPageParamString();
        $paramsArray[] = $this->getProviderParamString();
        $paramsArray[] = $this->getQueryParamString();
        return implode('&', $paramsArray);
    }
    
    private function getPageParamString() {
        return self::PAGE . '='. $this->page;
    }
    private function getProviderParamString() {
        return self::PROVIDER . '='. $this->provider;
    }
    
    private function getQueryParamString() {
        return self::QUERY . '=' . $this->query;
    }
    
}
