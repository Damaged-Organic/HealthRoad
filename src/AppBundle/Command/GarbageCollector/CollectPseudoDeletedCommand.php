<?php
// AppBundle/Command/GarbageCollector/CollectPseudoDeletedCommand.php
namespace AppBundle\Command\GarbageCollector;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class CollectPseudoDeletedCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dz:collect:pseudo_deleted')
            ->setDescription('Remove pseudo deleted entities')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer();
    }
}