<?php

namespace App\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use App\AppBundle\Service\Provider\Controller as ProviderController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;


class ListController extends FOSRestController implements ClassResourceInterface {

    public function cgetAction(Request $request) {
//        var_dump($request->query->all());
//        die();
        set_time_limit(120);
        /* @var $service \App\AppBundle\Service\GetAllTorrentsExecute */
        $service = $this->get('get_all_torrents_execute');
        $service->setRequest($request->query->all());
        $list = $service->getAllTorrentsAsArray();
        return $list;
    }
    
   
}
