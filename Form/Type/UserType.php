<?php

namespace ScoutEvent\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email');
        $builder->add('password', 'repeated', array(
           'first_name'  => 'password',
           'second_name' => 'confirm',
           'type'        => 'password'
        ));
        $builder->add('roles', new UserRoleType($options['em']));
        $builder->add('Create', 'submit', array(
            'attr'=> array(
                'class' => 'btn btn-success pull-right'
            )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ScoutEvent\BaseBundle\Entity\User'
        ))->setRequired(array(
            'em'
        ))->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}
