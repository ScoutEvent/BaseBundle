<?php

namespace ScoutEvent\BaseBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class RouteExtension extends \Twig_Extension
{

    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('pathExists', array($this, 'pathExists')),
        );
    }

    public function pathExists($route)
    {
        return $this->router->getRouteCollection()->get($route) !== null;
    }

    public function getName()
    {
        return 'route_extension';
    }

}
