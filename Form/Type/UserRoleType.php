<?php

namespace ScoutEvent\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;

class UserRoleType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $em;
    
    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $rsm = new ResultSetMapping;
        $rsm->addScalarResult('role', 'role');
        
        $query = $this->em->createQuery('select distinct r.role from ScoutEvent\BaseBundle\Entity\UserRole r');
        $roles = $query->getResult();
        
        $choices = array();
        foreach ($roles as $role) {
            $choices[$role["role"]] = $role["role"];
        }
    
        $resolver->setDefaults(array(
            'choices' => $choices,
            'multiple' => true
        ));
    }
    
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'user_role';
    }
}
