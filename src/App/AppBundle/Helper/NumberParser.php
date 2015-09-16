<?php

namespace App\AppBundle\Helper;

class NumberParser {
    
    const SIZE_TYPE_KB = 'KB';
    const SIZE_TYPE_MB = 'MB';
    const SIZE_TYPE_GB = 'GB';
    
    /**
     * Parses given string to integer. String is file size with, KB, MB, GB and 
     * returns value in bytes as integer.
     * 
     * @param type $stringNumber
     * @param type $sizeType kB, KB, MB, GB
     * 
     * @return integer in MB
     */
    public static function parseSizeToInteger($stringNumber, $sizeType) {
        switch(strtoupper($sizeType)) {
            case self::SIZE_TYPE_KB:
                return self::parseSizeKB($stringNumber);
            case self::SIZE_TYPE_MB:
                return self::parseSizeMB($stringNumber);
            case self::SIZE_TYPE_GB:
                return self::parseSizeGB($stringNumber);
        }
    }
    
    private static function parseSizeKB($string) {
        return $string / 1000;
    }
    
    private static function parseSizeMB($string) {
        return (float)$string ;
    }
    
    private static function parseSizeGB($string) {
        return $string * 1000;
    }
    
}