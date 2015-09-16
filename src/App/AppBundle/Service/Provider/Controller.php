<?php

namespace App\AppBundle\Service\Provider;

class Controller {
    
    const TORRENTHOUND = 'TORRENTHOUND';
    const TORRENTDOWNLOADS = 'TORRENTDOWNLOADS';
    const BITSNOOP = 'BITSNOOP';
    const PIRATEBAYORG = 'PIRATEBAYORG';
    const TORRENTREACTOR = 'TORRENTREACTOR';
    const EXTRATORRENT = 'EXTRATORRENT';
    const KICKASSTORRENT = 'KICKASSTORRENT';
    const MONONOVA = 'MONONOVA';
    const LIMETORRENTS = 'LIMETORRENTS';
    const ISOHUNT = 'ISOHUNT';
    
    private $providerCode;
    
    public function getProviderCode() {
        return $this->providerCode;
    }

    public function setProviderCode($providerCode) {
        $this->providerCode = $providerCode;
    }

    public function getProvider() {
        switch($this->providerCode) {
            case self::TORRENTHOUND:
                return new Torrenthound();
            case self::TORRENTDOWNLOADS:
                return new Torrentdownloads();
            case self::BITSNOOP:
                return new Bitsnoop();
            case self::PIRATEBAYORG:
                return new PirateBayOrg();
            case self::TORRENTREACTOR:
                return new Torrentreactor();
            case self::EXTRATORRENT:
                return new Extratorrent();
            case self::KICKASSTORRENT:
                return new Kickasstorrent();
            case self::MONONOVA:
                return new Mononova();
            case self::LIMETORRENTS:
                return new Limetorrents();
            case self::ISOHUNT:
                return new Isohunt();
            default:
                die('Provider not setted in '. __FILE__ . '::' . __LINE__);
        }
    }
    
}