<?php

namespace ScoutEvent\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use ScoutEvent\BaseBundle\Form\Type\UserType;
use ScoutEvent\BaseBundle\Form\Type\UserEditType;
use ScoutEvent\BaseBundle\Entity\User;
use ScoutEvent\BaseBundle\Entity\UserRole;

class UsersController extends Controller
{
    public function listAction()
    {
        $users = $this->getDoctrine()->getRepository('ScoutEventBaseBundle:User')->findAll();
        
        return $this->render(
            'ScoutEventBaseBundle:Users:list.html.twig',
            array('users' => $users)
        );
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new UserType(), new User(), array(
            'em' => $this->getDoctrine()->getManager(),
            'action' => $this->generateUrl('scout_base_user_create')
        ));
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $form->getData();
            
            //encrypt user password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            //generate password
            $plainPassword = $user->getPassword();
            $salt = $user->getSalt();
            $password = $encoder->encodePassword($plainPassword, $salt);

            if (!$encoder->isPasswordValid($password, $plainPassword, $salt)) {
                throw new \Exception('Password incorrectly encoded during user registration');
            } else {
                $user->setPassword($password);
            }

            // Save the roles, remove them and then add again after persisting
            // otherwise we have issues with the id not being set in User
            $roles = $user->getRoles();
            $user->setRoles(array());
            $em->persist($user);
            $em->flush();
            $user->setRoles($roles);
            $em->flush();

            return $this->redirect($this->generateUrl('scout_base_user_list'));
        }

        return $this->render(
            'ScoutEventBaseBundle:Users:create.html.twig',
            array('form' => $form->createView())
        );
    }

    public function editAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ScoutEventBaseBundle:User')->find($userId);
        $previousPassword = $user->getPassword();
    
        $form = $this->createForm(new UserEditType(), $user, array(
            'em' => $em,
            'action' => $this->generateUrl('scout_base_user_edit', array("userId" => $userId))
        ));
        $form->handleRequest($request);

        if ($form->get('Delete')->isClicked()) {
            $em->remove($user);
            $em->flush();
            return $this->redirect($this->generateUrl('scout_base_user_list'));
        } else if ($form->isValid()) {
            $user = $form->getData();
            
            //encrypt user password
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            //generate password
            $plainPassword = $user->getPassword();
            if ($plainPassword == '') {
                $user->setPassword($previousPassword);
            } else {
                $salt = $user->getSalt();
                $password = $encoder->encodePassword($plainPassword, $salt);

                if (!$encoder->isPasswordValid($password, $plainPassword, $salt)) {
                    throw new \Exception('Password incorrectly encoded during user registration');
                } else {
                    $user->setPassword($password);
                }
            }

            $query = $em->createQuery('select r.role from ScoutEvent\BaseBundle\Entity\UserRole r where r.user = :user');
            $query->setParameter("user", $user);
            $existingRoles = $query->execute();
            $newRoles = $user->getRoles();
            foreach ($existingRoles as $role) {
                if (!in_array($role, $newRoles)) {
                    $query = $em->createQuery('delete from ScoutEvent\BaseBundle\Entity\UserRole r where r.user = :user and r.role = :role');
                    $query->setParameter("user", $user);
                    $query->setParameter("role", $role);
                    $query->execute();
                }
            }
            $em->flush();

            return $this->redirect($this->generateUrl('scout_base_user_list'));
        }

        return $this->render(
            'ScoutEventBaseBundle:Users:edit.html.twig',
            array('form' => $form->createView())
        );
    }
}
