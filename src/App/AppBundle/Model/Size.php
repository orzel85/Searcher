<?php

namespace App\AppBundle\Model;

class Size{
    
    const SIZE_TYPE_KB = 'KB';
    const SIZE_TYPE_MB = 'MB';
    const SIZE_TYPE_GB = 'GB';
    
    public $value;
    
    public $type;
    
    public static function getSizeTypesAsArray() {
        return array(
            self::SIZE_TYPE_KB,
            self::SIZE_TYPE_MB,
            self::SIZE_TYPE_GB,
        );
    }
    
}

