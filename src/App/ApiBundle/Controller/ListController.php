<?php

namespace App\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use App\AppBundle\Service\Provider\Controller as ProviderController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;


class ListController extends FOSRestController implements ClassResourceInterface {

    public function cgetAction(Request $request) {
        $service = $this->get('get_all_torrents_execute');
        $service->setRequest($request->query->all());
        $list = $service->getAllTorrentsAsArray();
        return $list;
    }
    
   
}
