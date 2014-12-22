<?php

namespace ScoutEvent\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function appListAction()
    {
        $router = $this->container->get('router');
        $collection = $router->getRouteCollection();
        $allRoutes = $collection->all();

        $applist = array();
        foreach ($allRoutes as $name => $route) {
            $defaults = $route->getDefaults();
            if (array_key_exists("application", $defaults)) {
                $app = $defaults["application"];
                if (array_key_exists("roles", $defaults)) {
                    $granted = false;
                    foreach ($defaults["roles"] as $role) {
                        if (true === $this->get('security.context')->isGranted($role)) {
                            $granted = true;
                            break;
                        }
                    }
                    if ($granted == false) {
                        // Can't view this page
                        continue;
                    }
                }
                $applist[] = array(
                    "large" => array_key_exists("large", $app) ? $app["large"] == "true" : false,
                    "icon" => array_key_exists("icon", $app) ? $app["icon"] : false,
                    "content" => array_key_exists("name", $app) ? $app["name"] : false,
                    "link" => $this->generateUrl($name),
                );
            }
        }
    
        return $this->render('ScoutEventBaseBundle:Default:applist.html.twig', array('applist' => $applist));
    }
}
