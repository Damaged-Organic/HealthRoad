<?php
// AppBundle/Service/Report/ReportExcelAccounting.php
namespace AppBundle\Service\Report;

use DateTime;

use AppBundle\Service\Report\Utility\Extended\ReportExcel;

class ReportExcelAccounting extends ReportExcel
{
    const ACCOUNTING_DIRECTORY = 'accounting';

    public function getRootDirectory()
    {
        return parent::getRootDirectory() . "/" . self::ACCOUNTING_DIRECTORY;
    }

    public function getAccountingReportObject(array $accountingData)
    {
        $this->phpExcelObject = $this->createPhpExcelObject();

        $this->setProperties();

        $this->phpExcelObject->setActiveSheetIndex(0);

        $this->buildHeader();

        list($currentRow, $purchaseTotalAmount, $purchaseTotalSum) = $this->buildBody($accountingData);

        $this->buildFooter($currentRow, $purchaseTotalAmount, $purchaseTotalSum);

        $this
            ->adjustCellWidth()
            ->adjustRowHigh()
        ;

        $this->phpExcelObject->getActiveSheet()->setTitle('Общая');

        return $this->phpExcelObject;
    }

    private function setProperties()
    {
        $this->phpExcelObject->getProperties()
            ->setCreator("Генератор отчетов системы \"Дорога Здоровья\"")
            ->setLastModifiedBy("Генератор отчетов системы \"Дорога Здоровья\"")
            ->setTitle("Отчет для бухгалтеров")
            ->setSubject("Отчет по продажам сети за торговый день")
            ->setCategory("Отчет")
        ;
    }

    private function buildHeader()
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 1, "Отчет по продажам сети за торговый день")
            ->mergeCells($this->getPosition(1, 1))
        ;

        $this
            ->styleAlignHorizontalCenter(1, 1)
            ->styleFontBold(1, 1)
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(6, 2, "Дата:")
            ->setCellValueByColumnAndRow(7, 2, (new DateTime)->modify('yesterday')->format('m/d/Y'))
        ;

        $this->styleFontBold(2, 2, 'G');

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 4, "№ п/п")
            ->setCellValueByColumnAndRow(1, 4, "Дата")
            ->setCellValueByColumnAndRow(2, 4, "Категория")
            ->setCellValueByColumnAndRow(3, 4, "Артикул")
            ->setCellValueByColumnAndRow(4, 4, "Название товара")
            ->setCellValueByColumnAndRow(5, 4, "Количество")
            ->setCellValueByColumnAndRow(6, 4, "Цена")
            ->setCellValueByColumnAndRow(7, 4, "Стоимость")
        ;

        $this
            ->styleBorderThick(4, 4)
            ->styleFontBold(4, 4)
        ;
    }

    private function buildBody(array $accountingData)
    {
        $currentRow = 5;

        $itemCounter = 1;

        $purchaseTotal = [
            'amount' => 0,
            'sum'    => 0
        ];

        foreach( $accountingData as $category => $purchases )
        {
            $currentRow++;

            $purchaseTotalCategory = [
                'amount' => 0,
                'sum'    => 0
            ];

            foreach( $purchases as $purchaseData )
            {
                $currentPurchase = $purchaseData[0];

                $this->phpExcelObject->getActiveSheet()
                    ->setCellValueByColumnAndRow(0, $currentRow, $itemCounter)
                    ->setCellValueByColumnAndRow(1, $currentRow, $currentPurchase->getSyncPurchasedAt()->format('m/d/Y'))
                    ->setCellValueByColumnAndRow(2, $currentRow, $currentPurchase->getProduct()->getProductCategory()->getName())
                    ->setCellValueByColumnAndRow(3, $currentRow, NULL)
                    ->setCellValueByColumnAndRow(4, $currentRow, $currentPurchase->getProduct()->getNameFull())
                    ->setCellValueByColumnAndRow(5, $currentRow, $purchaseData['purchaseAmount'])
                    ->setCellValueByColumnAndRow(6, $currentRow, $currentPurchase->getProduct()->getPrice())
                    ->setCellValueByColumnAndRow(7, $currentRow, $purchaseData['purchaseSum'])
                ;

                $this->styleBorderThin($currentRow, $currentRow);

                $itemCounter++;

                $currentRow++;

                $purchaseTotalCategory = [
                    'amount' => $purchaseTotalCategory['amount'] + $purchaseData['purchaseAmount'],
                    'sum'    => bcadd($purchaseTotalCategory['sum'], $purchaseData['purchaseSum'], 2)
                ];
            }

            $this->phpExcelObject->getActiveSheet()
                ->setCellValueByColumnAndRow(4, $currentRow, "Итого в категории \"{$category}\":")
                ->setCellValueByColumnAndRow(5, $currentRow, $purchaseTotalCategory['amount'])
                ->setCellValueByColumnAndRow(7, $currentRow, $purchaseTotalCategory['sum'])
            ;

            $this
                ->styleBorderThin($currentRow, $currentRow)
                ->styleFillSolid($currentRow, $currentRow, 'FFFF00')
                ->styleFontBold($currentRow, $currentRow)
            ;

            $currentRow++;

            $purchaseTotal = [
                'amount' => $purchaseTotal['amount'] + $purchaseTotalCategory['amount'],
                'sum'    => bcadd($purchaseTotal['sum'], $purchaseTotalCategory['sum'], 2)
            ];
        }

        return [$currentRow, $purchaseTotal['amount'], $purchaseTotal['sum']];
    }

    private function buildFooter($currentRow, $purchaseTotalAmount, $purchaseTotalSum)
    {
        $currentRow++;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(4, $currentRow, "Итого по всем категориям:")
            ->setCellValueByColumnAndRow(5, $currentRow, $purchaseTotalAmount)
            ->setCellValueByColumnAndRow(7, $currentRow, $purchaseTotalSum)
        ;

        $this
            ->styleBorderThick($currentRow, $currentRow)
            ->styleFillSolid($currentRow, $currentRow, '558ED5')
            ->styleFontBold($currentRow, $currentRow)
        ;
    }
}