<?php

namespace App\AppBundle\Helper;

use App\AppBundle\Model\Size;

class SizeParser {
    
    /**
     * 
     * @param type $stringSize string that contains size and space is &nbsp;
     * 
     * @return Size
     */
    public static function explodeSizeByNbsp($stringSize) {
        $stringSize = str_replace('i', '', $stringSize);
        $size = new Size();
        $length = strlen($stringSize);
        $size->value = (float)substr($stringSize, 0, $length-5);
        $size->type = $stringSize[$length-2] . $stringSize[$length-1];
        return $size;
    }
    
    /**
     * Explodes string in format [sizeNumber][space][sizeType] i.e.: 11.6 GB
     * 
     * @param type $stringSize
     * @return Size
     */
    public static function explodeSizeBySpace($stringSize) {
        $sizeArray = explode(' ', $stringSize);
        $size = new Size();
        $size->type = $sizeArray[1];
        $size->value = $sizeArray[0];
        return $size;
    }
    
}
