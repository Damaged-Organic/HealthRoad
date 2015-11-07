<?php
// AppBundle/Command/Report/ReportAccountingCommand.php
namespace AppBundle\Command\Report;

use Exception;

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
        $_container = $this->getContainer();

        $_reportBuilder         = $_container->get('app.report.builder');
        $_reportExcelAccounting = $_container->get('app.report.excel.accounting');
        $_reportMailer          = $_container->get('app.report.mailer');

        $accountingData = $_reportBuilder->getAccountingData();

        if( $accountingData )
        {
            $preparedAccountingData = $_reportBuilder->prepareAccountingData($accountingData);

            if( !$preparedAccountingData )
                throw new Exception('CODE_1: Failed to prepare accounting data');

            $phpExcelObject = $_reportExcelAccounting->getAccountingReportObject($preparedAccountingData);

            if( !$phpExcelObject )
                throw new Exception('CODE_2: Failed to build excel report with accounting data');

            $filePath = $_reportExcelAccounting->savePhpExcelObject($phpExcelObject);

            if( !$filePath )
                throw new Exception('CODE_3: Failed to save excel report with accounting data');
        } else {
            $filePath = NULL;
        }

        if (!$_reportMailer->sendReportAccounting($filePath))
            throw new Exception('CODE_4: Failed to send accounting data report');
    }
}