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


class TorrentController extends FOSRestController implements ClassResourceInterface {

    /**
     * @ApiDoc(
     *      description="List of torrents from one vendor",
     *      section="TorrentController"
     * )
     * 
     * @Get("/torrents")
     * 
     * @REST\QueryParam(name="query", strict=true, nullable=false)
     * @REST\QueryParam(name="provider", nullable=false, description="Code of single provider.(String)")
     * @REST\QueryParam(name="page", requirements="\d+", nullable=false)
     */
    public function cgetAction(Request $request) {
        set_time_limit(300);
        /* @var $service \App\AppBundle\Service\TorrentsListExecute */
        $service = $this->get('torrents_list_execute');
        $service->setRequest($request->query->all());
        $list = $service->getTorrentsListAsArray();
        return $list;
    }
    
   
}
