<?php

namespace App\AppBundle\Helper\Filter;

class SeedsFilter {
    
    public static function getLimitedBySeeds($list, $minSeedsNumber) {
        $newArray = array();
        if(empty($list)) {
            return $newArray;
        }
        foreach($list as $torrent) {
            if($torrent->getSeeds() >= $minSeedsNumber) {
                $newArray[] = $torrent;
            }
        }
        return $newArray;
    }
    
}
