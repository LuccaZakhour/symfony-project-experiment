<?php

namespace App\Command\Deployment;

use App\Entity\SystemSetting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateSystemSettingCommand extends Command
{
    protected static $defaultName = 'deployment:update-system-setting';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates a system setting')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'The name of the setting')
            ->addOption('value', null, InputOption::VALUE_REQUIRED, 'The new value of the setting');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        $value = $input->getOption('value');

        $repository = $this->em->getRepository(SystemSetting::class);
        $setting = $repository->findOneBy(['name' => $name]);

        if (!$setting) {
            $output->writeln("<info>Setting with name '$name' not found. Creating new one.</info>");
            $setting = new SystemSetting();
            $setting->setName($name);
        }

        $setting->setValue($value);
        $this->em->persist($setting); // This is safe to call even if $setting already exists
        $this->em->flush();

        $output->writeln("<info>Setting updated successfully!</info>");
        return Command::SUCCESS;
    }

}
