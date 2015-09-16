<?php

namespace App\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use App\AppBundle\Service\Provider\Controller as ProviderController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;


class TorrentController extends FOSRestController implements ClassResourceInterface {

    public function cgetAction(Request $request) {
        $service = $this->get('torrents_list_execute');
        $service->setRequest($request->request->all());
        $list = $service->getTorrentsListAsArray();
        return $list;
    }
    
   
}
