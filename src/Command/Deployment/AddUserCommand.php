<?php

namespace App\Command\Deployment;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class AddUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasher $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)#, PasswordHasherInterface $passwordHasher
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        parent::__construct(null);
    }

    protected function configure()
    {
        $this->setName('deployment:add-user');

        $this->addArgument('adminEmail', InputArgument::REQUIRED);
        $this->addArgument('adminPassword', InputArgument::REQUIRED);
        $this->addArgument('demoEnd', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $user = new User();

        $user->setEmail($input->getArgument('adminEmail'));
        $user->setSalutation('');
        $user->setFirstname('');
        $user->setLastname('Administrator');
        $user->setRoles([User::ROLE_ADMIN, User::ROLE_USER]);

        #$encodedPassword = $this->passwordHasher->hash($input->getArgument('adminPassword'));
        $user->setPassword($input->getArgument('adminPassword'));
        $user->hashPassword($this->passwordHasher);

        if ($input->getArgument('demoEnd')) {
            $dateTimeStr = $input->getArgument('demoEnd');
            $dateTime = DateTime::createFromFormat('d.m.Y-H:i', $dateTimeStr);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
