<?php

namespace App\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use App\AppBundle\Entity\Query;

class QueryRepository extends EntityRepository {
    
    public function findByQuery($query, $page, $providerCode) {
        $querySha1 =  $this->getSha1FromQuery($query);
        $queryObj = $this->findOneBy(array('valueSha1' => $querySha1, 'page' => $page, 'provider' => $providerCode));
        return $queryObj;
    }
    
    /*
     * @return \App\AppBundle\Entity\Query
     */
    public function create($query, $provider, $page) {
        $queryObj = new Query();
        $queryObj->setValue($query);
        $queryObj->setValueSha1($this->getSha1FromQuery($query));
        $queryObj->setCreationDate(new \DateTime(date('Y-m-d H:i:s')));
        $queryObj->setUpdateDate(new \DateTime(date('Y-m-d H:i:s')));
        $queryObj->setProvider($provider);
        $queryObj->setPage($page);
        $this->getEntityManager()->persist($queryObj);
        $this->getEntityManager()->flush();
        return $queryObj;
    }
    
    public function updateUpdateDateToCurrentDate(Query $query) {
        $query->setUpdateDate(new \DateTime(date('Y-m-d H:i:s')));
        $this->getEntityManager()->persist($query);
        $this->getEntityManager()->flush();
        return $query;
    }
    
    private function getSha1FromQuery($query) {
        $queryArr = explode(' ', $query);
        sort($queryArr);
        $newQuery = implode(' ', $queryArr);
        return sha1($newQuery);
    }
    
}