<?php
// AppBundle/Service/Report/ReportExcelLogistics.php
namespace AppBundle\Service\Report;

use AppBundle\Entity\School\School;
use DateTime;

use AppBundle\Service\Report\Utility\Extended\ReportExcel,
    AppBundle\Entity\VendingMachine\VendingMachine;

class ReportExcelLogistics extends ReportExcel
{
    const COLUMN_START = 'B';
    const COLUMN_END   = 'J';

    const LOGISTICS_DIRECTORY = 'logistics';

    public function getRootDirectory()
    {
        return parent::getRootDirectory() . "/" . self::LOGISTICS_DIRECTORY;
    }

    public function getLogisticsReportObject(array $logisticsData)
    {
        $this->phpExcelObject = $this->createPhpExcelObject();

        $this->setProperties();

        foreach($logisticsData as $key => $vendingMachine)
        {
            $this->phpExcelObject->setActiveSheetIndex($key);

            $this->buildHeader($vendingMachine['object']);

            $this->buildReportTable($vendingMachine['sum']);

            if( $vendingMachine['object']->getSchool() )
                $this->buildLocationTable($vendingMachine['object']->getSchool());

            $this->buildBody($vendingMachine['object']);

            $this
                ->adjustCellWidth()
                ->adjustRowHigh()
            ;

            $title = ( $vendingMachine['object']->getSchool() )
                ? $vendingMachine['object']->getSchool()->getFullAddress()
                : "Лист {$key}";

            $this->phpExcelObject->getActiveSheet()->setTitle($title);
        }

        $this->phpExcelObject->setActiveSheetIndex(0);

        return $this->phpExcelObject;
    }

    protected function setProperties()
    {
        $this->phpExcelObject->getProperties()
            ->setCreator("Генератор отчетов системы \"Дорога Здоровья\"")
            ->setLastModifiedBy("Генератор отчетов системы \"Дорога Здоровья\"")
            ->setTitle("Отчет для логистики")
            ->setSubject("Отчет с картами продаж торговых автоматов")
            ->setCategory("Отчет")
        ;
    }

    protected function buildHeader(VendingMachine $vendingMachine)
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 1, "# " . ($this->phpExcelObject->getActiveSheetIndex() + 1))
        ;

        $this
            ->styleAlignHorizontalCenter(1, 1, 'A')
            ->styleFontBold(1, 1, 'A')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(1, 1, "Карта продаж автомата " . $vendingMachine->getChoiceLabel())
            ->mergeCells($this->getPosition(1, 1))
        ;

        $this
            ->styleAlignHorizontalCenter(1, 1)
            ->styleFontBold(1, 1)
        ;
    }

    protected function buildReportTable($sum)
    {
        // Purchase Sum

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(1, 3, "Сумма продаж:")
            ->mergeCells($this->getPosition(3, 3, 'B') . ":" . $this->getPosition(3, 3, 'C'))
        ;

        $this
            ->styleAlignHorizontalRight(3, 3, 'B')
            ->styleBorderThin(3, 3, 'B')
            ->styleFontBold(3, 3, 'B')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(3, 3, $sum)
            ->mergeCells($this->getPosition(3, 3, 'D') . ":" . $this->getPosition(3, 3, 'E'))
        ;

        $this
            ->styleAlignHorizontalRight(3, 3, 'D')
            ->styleAlignHorizontalRight(3, 3, 'E')
            ->styleBorderThin(3, 3, 'D')
            ->styleBorderThin(3, 3, 'E')
        ;

        // Date

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(1, 4, "Дата:")
            ->mergeCells($this->getPosition(4, 4, 'B') . ":" . $this->getPosition(4, 4, 'C'))
        ;

        $this
            ->styleAlignHorizontalRight(4, 4, 'B')
            ->styleBorderThin(4, 4, 'B')
            ->styleFontBold(4, 4, 'B')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(3, 4, (new DateTime)->format('m/d/Y'))
            ->mergeCells($this->getPosition(4, 4, 'D') . ":" . $this->getPosition(4, 4, 'E'))
        ;

        $this
            ->styleAlignHorizontalRight(4, 4, 'D')
            ->styleAlignHorizontalRight(4, 4, 'E')
            ->styleBorderThin(4, 4, 'D')
            ->styleBorderThin(4, 4, 'E')
        ;

        // Time

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(1, 5, "Время:")
            ->mergeCells($this->getPosition(5, 5, 'B') . ":" . $this->getPosition(5, 5, 'C'))
        ;

        $this
            ->styleAlignHorizontalRight(5, 5, 'B')
            ->styleBorderThin(5, 5, 'B')
            ->styleFontBold(5, 5, 'B')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(3, 5, (new DateTime)->format('H:i'))
            ->mergeCells($this->getPosition(5, 5, 'D') . ":" . $this->getPosition(5, 5, 'E'))
        ;

        $this
            ->styleAlignHorizontalRight(5, 5, 'D')
            ->styleAlignHorizontalRight(5, 5, 'E')
            ->styleBorderThin(5, 5, 'D')
            ->styleBorderThin(5, 5, 'E')
        ;
    }

    protected function buildLocationTable(School $school)
    {
        // Region

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(6, 3, "Регион:")
            ->mergeCells($this->getPosition(3, 3, 'G') . ":" . $this->getPosition(3, 3, 'H'))
        ;

        $this
            ->styleAlignHorizontalRight(3, 3, 'G')
            ->styleBorderThin(3, 3, 'G')
            ->styleFontBold(3, 3, 'G')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(8, 3, ( $school->getRegion() ) ? $school->getRegion()->getName() : '-' )
            ->mergeCells($this->getPosition(3, 3, 'I') . ":" . $this->getPosition(3, 3, 'J'))
        ;

        $this
            ->styleAlignHorizontalRight(3, 3, 'I')
            ->styleAlignHorizontalRight(3, 3, 'J')
            ->styleBorderThin(3, 3, 'I')
            ->styleBorderThin(3, 3, 'J')
        ;

        // Settlement

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(6, 4, "Населенный пункт:")
            ->mergeCells($this->getPosition(4, 4, 'G') . ":" . $this->getPosition(4, 4, 'H'))
        ;

        $this
            ->styleAlignHorizontalRight(4, 4, 'G')
            ->styleBorderThin(4, 4, 'G')
            ->styleFontBold(4, 4, 'G')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(8, 4, ( $school->getSettlement()->getName() ) ?: '-' )
            ->mergeCells($this->getPosition(4, 4, 'I') . ":" . $this->getPosition(4, 4, 'J'))
        ;

        $this
            ->styleAlignHorizontalRight(4, 4, 'I')
            ->styleAlignHorizontalRight(4, 4, 'J')
            ->styleBorderThin(4, 4, 'I')
            ->styleBorderThin(4, 4, 'J')
        ;

        // Address

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(6, 5, "Адрес:")
            ->mergeCells($this->getPosition(5, 5, 'G') . ":" . $this->getPosition(5, 5, 'H'))
        ;

        $this
            ->styleAlignHorizontalRight(5, 5, 'G')
            ->styleBorderThin(5, 5, 'G')
            ->styleFontBold(5, 5, 'G')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(8, 5, ( $school->getAddress() ) ?: '-' )
            ->mergeCells($this->getPosition(5, 5, 'I') . ":" . $this->getPosition(5, 5, 'J'))
        ;

        $this
            ->styleAlignHorizontalRight(5, 5, 'I')
            ->styleAlignHorizontalRight(5, 5, 'J')
            ->styleBorderThin(5, 5, 'I')
            ->styleBorderThin(5, 5, 'J')
        ;

        // Name

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(6, 6, "Школа:")
            ->mergeCells($this->getPosition(6, 6, 'G') . ":" . $this->getPosition(6, 6, 'H'))
        ;

        $this
            ->styleAlignHorizontalRight(6, 6, 'G')
            ->styleBorderThin(6, 6, 'G')
            ->styleFontBold(6, 6, 'G')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(8, 6, ( $school->getName() ) ?: '-' )
            ->mergeCells($this->getPosition(6, 6, 'I') . ":" . $this->getPosition(6, 6, 'J'))
        ;

        $this
            ->styleAlignHorizontalRight(6, 6, 'I')
            ->styleAlignHorizontalRight(6, 6, 'J')
            ->styleBorderThin(6, 6, 'I')
            ->styleBorderThin(6, 6, 'J')
        ;
    }

    protected function buildBody(VendingMachine $vendingMachine)
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(1, 8, "№ п/п")
            ->setCellValueByColumnAndRow(2, 8, "Категория")
            ->setCellValueByColumnAndRow(5, 8, "Артикул")
            ->setCellValueByColumnAndRow(6, 8, "Наименование")
            ->setCellValueByColumnAndRow(9, 8, "Количество")
        ;

        $this->phpExcelObject->getActiveSheet()
            ->mergeCells($this->getPosition(8, 8, 'C') . ":" . $this->getPosition(8, 8, 'E'))
            ->mergeCells($this->getPosition(8, 8, 'G') . ":" . $this->getPosition(8, 8, 'I'))
        ;

        $this
            ->styleAlignHorizontalCenter(8, 8)
            ->styleBorderThick(8, 8)
            ->styleFontBold(8, 8)
        ;

        $currentRow = 9;

        foreach( $vendingMachine->getPurchases() as $purchase )
        {
            $currentRow++;

            $currentPurchase = $purchase['object'];

            $this->phpExcelObject->getActiveSheet()
                ->setCellValueByColumnAndRow(1, $currentRow, $currentPurchase->getProduct()->getId())
                ->setCellValueByColumnAndRow(2, $currentRow, $currentPurchase->getProduct()->getProductCategory()->getName())
                ->setCellValueByColumnAndRow(5, $currentRow, $currentPurchase->getProduct()->getCode())
                ->setCellValueByColumnAndRow(6, $currentRow, $currentPurchase->getProduct()->getNameFull())
                ->setCellValueByColumnAndRow(9, $currentRow, $purchase['quantity'])
            ;

            $this->phpExcelObject->getActiveSheet()
                ->mergeCells($this->getPosition($currentRow, $currentRow, 'C') . ":" . $this->getPosition($currentRow, $currentRow, 'E'))
                ->mergeCells($this->getPosition($currentRow, $currentRow, 'G') . ":" . $this->getPosition($currentRow, $currentRow, 'I'))
            ;

            $this->styleBorderThin($currentRow, $currentRow);
        }
    }
}