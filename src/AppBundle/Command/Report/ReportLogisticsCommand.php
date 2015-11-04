<?php
// AppBundle/Command/Report/ReportLogisticsCommand.php
namespace AppBundle\Command\Report;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class ReportLogisticsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dz:report:logistics')
            ->setDescription('Send Logistics report')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$this->getContainer()->get()
        print "Logistics";
    }
}