<?php

namespace App\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\AppBundle\Service\Provider\Controller as ProviderController;


class TestController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AppApiBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function listTorrenthoundAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("mr robot s01e05 720p");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::TORRENTHOUND);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listTorrentdownloadsAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("Harry Potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::TORRENTDOWNLOADS);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
//        var_dump($list);
//        var_dump(json_encode($list));
//        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listBitsnoopAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::BITSNOOP);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listPiratebayorgAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::PIRATEBAYORG);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listTorrentreactorAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter 1080p DTS");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::TORRENTREACTOR);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listKickasstorrentAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::KICKASSTORRENT);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listExtratorrentAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::EXTRATORRENT);
        $service->execute();
//        $list = $service->getTorrentsList();
        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function listMononovaAction($query, $page) {
        $service = $this->get('torrents_list');
        $service->setQueryFromRequest("harry potter");
        $service->setPage($page);
        $service->setProviderCode(ProviderController::MONONOVA);
        $service->execute();
        $list = $service->getTorrentsList();
//        $list = $service->getTorrentsListAsArray();
        var_dump($list);
        die();
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
    
    public function testAction() {
        $page = 2;
        $query = 'mr robot s01e04 720p';
        $query = 'harry potter';
        $service = $this->get('provider_controller');
        $service->setProviderCode(ProviderController::TORRENTHOUND);
        $provider = $service->getProvider();
        $provider->setPage($page);
        $provider->setQuery($query);
        $list = $provider->getTorrentList();
        var_dump($list);
        return $this->render('AppApiBundle:Torrent:list.html.twig', array('list' => $list));
    }
}
