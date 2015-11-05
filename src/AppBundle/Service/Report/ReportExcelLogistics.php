<?php
// AppBundle/Service/Report/ReportExcelLogistics.php
namespace AppBundle\Service\Report;

use DateTime;

use AppBundle\Service\Report\Utility\Extended\ReportExcel;

class ReportExcelLogistics extends ReportExcel
{
    const LOGISTICS_DIRECTORY = 'logistics';

    public function getRootDirectory()
    {
        return parent::getRootDirectory() . "/" . self::LOGISTICS_DIRECTORY;
    }

    public function getLogisticsReportObject(array $logisticsData)
    {
        // ---

        $this->phpExcelObject->getActiveSheet()->setTitle('Общая'); // On all pages ->

        $this->phpExcelObject->setActiveSheetIndex(0);

        // ---
    }
}