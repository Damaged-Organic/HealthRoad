<?php
// AppBundle/Command/Report/ReportLogisticsCommand.php
namespace AppBundle\Command\Report;

use Exception;

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
        $_container = $this->getContainer();

        $_reportBuilder                  = $_container->get('app.report.builder');
        $_reportExcelLogistics           = $_container->get('app.report.excel.logistics');
        $_reportExcelAccountingLogistics = $_container->get('app.report.excel.accounting_logistics');
        $_reportMailer                   = $_container->get('app.report.mailer');

        list($readyByPurchaseSum, $readyByLoadDate) = $_reportBuilder->getLogisticsData();

        // Building logistics report
        if( $readyByPurchaseSum && $readyByLoadDate )
        {
            $preparedLogisticsData = $_reportBuilder->prepareLogisticsData($readyByPurchaseSum, $readyByLoadDate);

            if( !$preparedLogisticsData )
                throw new Exception('CODE_1_1: Failed to prepare logistics data');

            $phpExcelObjectLogistics = $_reportExcelLogistics->getLogisticsReportObject($preparedLogisticsData);

            if( !$phpExcelObjectLogistics )
                throw new Exception('CODE_2_1: Failed to build excel report with accounting data');
        }

        $logisticsAccountingData = $_reportBuilder->getLogisticsAccountingData();

        // Building logistics accounting report
        if( $logisticsAccountingData )
        {
            $preparedAccountingData = $_reportBuilder->prepareAccountingData($logisticsAccountingData);

            if( !$preparedAccountingData )
                throw new Exception('CODE_1_2: Failed to prepare logistics accounting data');

            $phpExcelObjectAccountingLogistics = $_reportExcelAccountingLogistics->getAccountingReportObject($preparedAccountingData, count($readyByPurchaseSum));

            if( !$phpExcelObjectAccountingLogistics )
                throw new Exception('CODE_2_2: Failed to build excel report with logistics accounting data');
        }

        // Merging two reports in one logistics report
        if( !empty($phpExcelObjectLogistics) && !empty($phpExcelObjectAccountingLogistics) )
        {
            $phpExcelObjectLogistics->addExternalSheet($phpExcelObjectAccountingLogistics->getActiveSheet());

            $filePath = $_reportExcelLogistics->savePhpExcelObject($phpExcelObjectLogistics);

            if( !$filePath )
                throw new Exception('CODE_3: Failed to save excel report with logistics data');
        } else {
            $filePath = NULL;
        }

        if( !$_reportMailer->sendReportLogistics($filePath) )
            throw new Exception('CODE_4: Failed to send logistics data report');
    }
}