<?php

namespace App\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use App\AppBundle\Entity\Torrents;

class TorrentsRepository extends EntityRepository {
  
    /*
     * @return \App\AppBundle\Entity\Query
     */
    public function create($link, $name, $seeds, $peers, $provider, $size, $sizeOriginal, $withFlush = true) {
        $entity = new Torrents();
        $entity->setName($name);
        $entity->setLink($link);
        $entity->setLinkSha1($this->getSha1($link));
        $entity->setSeeds($seeds);
        $entity->setPeers($peers);
        $entity->setSize($size);
        $entity->setSizeOriginal($sizeOriginal);
        $currentDate = new \DateTime(date('Y-m-d H:i:s'));
        $entity->setCreateDate($currentDate);
        $entity->setUpdateDate($currentDate);
        $entity->setProvider($provider);
        $this->getEntityManager()->persist($entity);
        if($withFlush) {
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
    
    public function flush() {
        $this->getEntityManager()->flush();
    }
    
    public function updateTorrent(Torrents $entity, $seeds, $peers, $withFlush = true) {
        $entity->setSeeds($seeds);
        $entity->setPeers($peers);
        $currentDate = new \DateTime(date('Y-m-d H:i:s'));
        $entity->setUpdateDate($currentDate);
        $this->getEntityManager()->persist($entity);
        if($withFlush) {
            $this->getEntityManager()->flush();
        }
        return $entity;
    }
    
    public function updateCreateTorrentList(array $newTorrentList) {
        $linksSha1 = $this->getLinkSha1ArrayFromTorrentsList($newTorrentList);
        $dbTorrentsList = $this->getTorrentsByLinkSha1($linksSha1);
        foreach($newTorrentList as $newTorrent) {
            if(key_exists($newTorrent->getLinkSha1(), $dbTorrentsList)) {
                //update
                $oldTorrent = $dbTorrentsList[$newTorrent->getLinkSha1()];
                $this->updateTorrent($oldTorrent, $newTorrent->getSeeds(), $newTorrent->getPeers(), false);
            }else{
                //create
                $this->create($newTorrent->getLink(), $newTorrent->getName(), $newTorrent->getSeeds(), $newTorrent->getPeers(), $newTorrent->getProvider(), $newTorrent->getSize(), $newTorrent->getSizeOriginal(), false);
            }
        }
        $this->flush();
    }
    
    private function getLinkSha1ArrayFromTorrentsList(array $torrentsList) {
        $return = array();
        foreach($torrentsList as $torrent) {
            $return[] = $torrent->getLinkSha1();
        }
        return $return;
    }
    
    public function getTorrentsByLinkSha1(array $linksSha1) {
        if(empty($linksSha1)) {
            return null;
        }
        $list = $this->getTorrents($linksSha1);
        if(!is_array($list)) {
            $list = array($list);
        }
        /**
         * @var $obj Torrents
         */
        $listReturn = array();
        foreach($list as $obj) {
            $listReturn[$obj->getLinkSha1()] = $obj;
        }
        unset($list);
        return $listReturn;
    }
    
    private function getTorrents(array $linksSha1) {
        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.linkSha1 IN (:linksSha1)')
            ->setParameter('linksSha1', $linksSha1);
        $result = $queryBuilder->getQuery();
        return $result->getResult();
    }
    
    private function getSha1($value) {
        return sha1($value);
    }
    
}