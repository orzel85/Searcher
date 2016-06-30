<?php

namespace App\AppBundle\Model;

class Size{
    
    const SIZE_TYPE_KB = 'KB';
    const SIZE_TYPE_MB = 'MB';
    const SIZE_TYPE_GB = 'GB';
    
    public $value = 0;
    
    public $type = 'MB';
    
    public static function getSizeTypesAsArray() {
        return array(
            self::SIZE_TYPE_KB,
            self::SIZE_TYPE_MB,
            self::SIZE_TYPE_GB,
        );
    }
    
}

