<?php

namespace ScoutEvent\BaseBundle\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationHandler
implements AuthenticationSuccessHandlerInterface,
           AuthenticationFailureHandlerInterface
{
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if ($targetPath = $request->getSession()->get('_security.target_path')) {
            $url = $targetPath;
        } else {
            // Otherwise, redirect him to wherever you want
            $url = "/";
        }
        
        if ($request->isXmlHttpRequest()) {
            // Handle XHR here
            $response = new Response();

            $response->setContent($url);
            $response->setStatusCode(Response::HTTP_OK);
            $response->headers->set('Content-Type', 'text/plain');

            return $response;
        } else {
            // If the user tried to access a protected resource and was forces to login
            // redirect him back to that resource
            return new RedirectResponse($url);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            // Handle XHR here
            $response = new Response();

            $response->setContent('Failure');
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            $response->headers->set('Content-Type', 'text/plain');

            return $response;
        } else {
            // Create a flash message with the authentication error message
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());
            $url = "/";

            return new RedirectResponse($url);
        }
    }
}
