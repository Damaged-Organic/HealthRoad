<?php
// AppBundle/Command/Report/ReportAccountingCommand.php
namespace AppBundle\Command\Report;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

class ReportAccountingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dz:report:accounting')
            ->setDescription('Send Accounting report')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_reportBuilder = $this->getContainer()->get('app.report.builder');

        $accountingData = $_reportBuilder->prepareAccountingData($_reportBuilder->getAccountingData());

        \Doctrine\Common\Util\Debug::dump($accountingData, 2);
    }
}