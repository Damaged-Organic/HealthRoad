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

    //TODO: Add logging in case of error
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $_container = $this->getContainer();

        $_reportBuilder         = $_container->get('app.report.builder');
        $_reportExcelAccounting = $_container->get('app.report.excel.accounting');
        $_reportMailer          = $_container->get('app.report.mailer');

        $accountingData = $_reportBuilder->prepareAccountingData(
            $_reportBuilder->getAccountingData()
        );

        if( !$accountingData )
            return; //Except and log?

        $phpExcelObject = $_reportExcelAccounting->getAccountingReportObject($accountingData);

        if( !$phpExcelObject )
            return;

        $filePath = $_reportExcelAccounting->savePhpExcelObject($phpExcelObject);

        if( !$filePath )
            return;

        if( !$_reportMailer->sendReportAccounting($filePath) )
            return;
    }
}