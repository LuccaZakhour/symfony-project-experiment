<?php

namespace App\Command\Deployment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UpdatePasswordCommand extends Command
{
    protected static $defaultName = 'deployment:update-password';

    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates a user\'s password.')
            ->addArgument('email', InputArgument::REQUIRED, 'The user\'s email')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);

        if (!$user) {
            $output->writeln('User not found!');
            return Command::FAILURE;
        }

        $user->setPassword($password);
        $user->hashPassword($this->passwordHasher);


        // Persist the updated user entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Password updated successfully!');
        return Command::SUCCESS;
    }
}
