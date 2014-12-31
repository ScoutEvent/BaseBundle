<?php

namespace ScoutEvent\BaseBundle\Security\Authorization\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolverInterface;

class RouteVoter implements VoterInterface
{
    private $authenticationTrustResolver;
    protected $requestStack;

    public function __construct(AuthenticationTrustResolverInterface $authenticationTrustResolver, RequestStack $requestStack, Router $router)
    {
        $this->authenticationTrustResolver = $authenticationTrustResolver;
        $this->requestStack  = $requestStack;
        $this->router = $router;
    }

    public function supportsAttribute($attribute)
    {
        // you won't check against a user attribute, so return true
        return false;
    }

    public function supportsClass($class)
    {
        // your voter supports all type of token classes, so return true
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        // Get the routes from the router
        $collection = $this->router->getRouteCollection();
        $allRoutes = $collection->all();
        
        // Get the current route
        $request = $this->requestStack->getCurrentRequest();
        $defaults = $allRoutes[$request->get('_route')]->getDefaults();
        
        // Get the roles from the token
        $userRoles = $token->getRoles();

        // Check if the role requires authentication
        if (array_key_exists("roles", $defaults)) {
            if (count($defaults["roles"]) == 0) {
                // No roles in list
                return VoterInterface::ACCESS_ABSTAIN;
            }
        
            // Look if the role is in the user roles
            $granted = false;
            foreach ($defaults["roles"] as $role) {
                if ($role == "IS_AUTHENTICATED_FULLY" && $this->authenticationTrustResolver->isFullFledged($token)) {
                    $granted = true;
                } else {
                    foreach ($userRoles as $allowedRole) {
                        if ($allowedRole->getRole() == $role) {
                            $granted = true;
                            break;
                        }
                    }
                }
                if ($granted == true) {
                    break;
                }
            }
            
            // No valid intersection, so deny access
            if ($granted == false) {
                return VoterInterface::ACCESS_DENIED;
            }
        }

        // Allow something else to deny access if it wants to
        return VoterInterface::ACCESS_ABSTAIN;
    }
}
