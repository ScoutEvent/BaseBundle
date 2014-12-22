<?php

namespace ScoutEvent\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

use ScoutEvent\BaseBundle\Entity\User;

class Command extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('scoutevent:createuser')
            ->setDescription('Create a new user in the Scout Event system')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
    
        $question = new Question('User email address: ');
        $email = $helper->ask($input, $output, $question);

        $question = new Question('User password: ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);
        
        $em = $this->getContainer()->get('doctrine')->getManager();
        $factory = $this->getContainer()->get('security.encoder_factory');
        
        $user = new User();
        $user->setEmail($email);
        $user->setRawPassword($password, $factory);
        
        $em->persist($user);
        $em->flush();
    }
}