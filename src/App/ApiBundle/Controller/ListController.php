<?php

namespace App\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\FOSRestController;
use App\AppBundle\Service\Provider\Controller as ProviderController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\Annotations\Get;


class ListController extends FOSRestController implements ClassResourceInterface {

    /**
     * @ApiDoc(
     *      description="List of torrents",
     *      section="ListController"
     * )
     * 
     * @Get("/lists")
     * 
     * @REST\QueryParam(name="query", nullable=false)
     * @REST\QueryParam(name="page", requirements="\d+", nullable=false)
     */
    public function cgetAction(Request $request) {
        set_time_limit(120);
        /* @var $service \App\AppBundle\Service\GetAllTorrentsExecute */
        $service = $this->get('get_all_torrents_execute');
        $service->setRequest($request->query->all());
        $list = $service->getAllTorrentsAsArray();
        return $list;
    }
    
   
}
