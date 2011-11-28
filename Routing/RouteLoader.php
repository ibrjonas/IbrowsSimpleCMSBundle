<?php

namespace Ibrows\SimpleCMSBundle\Routing;

use Symfony\Component\Routing\RouteCollection as SymfonyRouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;

class RouteLoader extends FileLoader
{

    /**
     * @var \Ibrows\SimpleCMSBundle\Model\ContentManager
     */
    protected $manager;
    /**
     *
     * @var  \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    const ROUTE_BEGIN = 'scms_';
    
    /**
     * @param \Ibrows\SimpleCMSBundle\Model\ContentManager $pool
     */
    public function __construct(\Ibrows\SimpleCMSBundle\Model\ContentManager $manager, \Symfony\Component\Routing\RouterInterface $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    /**
     * @param string $resource
     * @param null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        if ($type == 'ibrows_router') {
            return true;
        }
        return false;
    }

    /**
     * @param string $resource
     * @param null $type
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function load($resource, $type = null)
    {
        $collection = new SymfonyRouteCollection();
        $repo = $this->manager->getRepository('metatags');
        /* @var $repo \Ibrows\SimpleCMSBundle\Repository\MetaTagRepository         */
        $results = $repo->findAllAlias();
        if(!$results ){
            return null;
        }
        foreach($results as $metatag){           
            $pathinfo = unserialize($metatag['pathinfo']);
            $oldroute = $pathinfo['_route'];
            $route = new Route($metatag['alias'],$pathinfo, array(), array());
            $collection->add(self::ROUTE_BEGIN.$oldroute, $route);
            
        }
        

        return $collection;        
    }
   
}