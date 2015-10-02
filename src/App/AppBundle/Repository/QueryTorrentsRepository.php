<?php

namespace App\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use App\AppBundle\Entity\QueryTorrents;
use App\AppBundle\Entity\Query;
use App\AppBundle\Entity\Torrents;

class QueryTorrentsRepository extends EntityRepository {
    
    /*
     * @return \App\AppBundle\Entity\Query
     */
    public function create(Query $query, Torrents $torrents, $provider, $page, $orderOnList, $withFlush = true) {
        $entity = new QueryTorrents();
        $entity->setQueryValueSha1($query->getValueSha1());
        $entity->setTorrentsLinkSha1($torrents->getLinkSha1());
        $entity->setPage($page);
        $entity->setOrderOnList($orderOnList);
        $entity->setProvider($provider);
        $currentDate = new \DateTime(date('Y-m-d H:i:s'));
        $entity->setCreateDate($currentDate);
        $entity->setUpdateDate($currentDate);
        $this->getEntityManager()->persist($entity);
        if($withFlush) {
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
    
    public function getTorrentsByQuery(Query $query, $providerCode, $page) {
        $list = $this->getTorrents($query, $providerCode, $page);
        $return = array();
        /**
         * @var $obj QueryTorrents
         */
        foreach($list as $obj) {
            $return[$obj->getOrderOnList()] = $obj;
        }
        unset($list);
        return $return;
    }
    
    /**
     * 
     * @param Query $query
     * @param array $newTorrentsList array of Torrents obj
     * @param type $providerCode
     * @param type $page
     */
    public function updateList(Query $query, $newTorrentsList, $providerCode, $page) {
        $oldList = $this->getTorrentsByQuery($query, $providerCode, $page);
        $newTorrentListLength = count($newTorrentsList);
        for($i = 0; $i < $newTorrentListLength ; $i++) {
            if(!empty($oldList[$i])) {
                $this->updateQueryTorrent($oldList[$i], $newTorrentsList[$i], false);
            }else{
                $this->create($query, $newTorrentsList[$i], $providerCode, $page, $i, false);
            }
        }
        $this->flush();
    }
    
    public function flush() {
        $this->getEntityManager()->flush();
    }
    
    public function updateQueryTorrent(QueryTorrents $entity, Torrents $torrents, $withFlush = true) {
        $entity->setTorrentsLinkSha1($torrents->getLinkSha1());
        $currentDate = new \DateTime(date('Y-m-d H:i:s'));
        $entity->setUpdateDate($currentDate);
        $this->getEntityManager()->persist($entity);
        if($withFlush) {
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
    
    private function getTorrents(Query $query, $providerCode, $page) {
        $queryBuilder = $this->createQueryBuilder('qt')
            ->where('qt.queryValueSha1 = :querySha1')
            ->setParameter('querySha1', $query->getValueSha1())
            ->andWhere('qt.page = :page')
            ->setParameter('page', $page)
            ->andWhere('qt.provider = :provider')
            ->setParameter('provider', $providerCode)
            ->getQuery();
        return $queryBuilder->getResult();
        
    }
    
}