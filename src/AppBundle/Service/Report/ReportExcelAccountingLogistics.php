<?php
// AppBundle/Service/Report/ReportExcelAccountingLogistics.php
namespace AppBundle\Service\Report;

use DateTime;

use AppBundle\Service\Report\ReportExcelAccounting;

class ReportExcelAccountingLogistics extends ReportExcelAccounting
{
    const COLUMN_START = 'A';
    const COLUMN_END   = 'G';

    const LOGISTICS_DIRECTORY = 'logistics';

    public function getAccountingReportObject(array $accountingData, $vendingMachineAmount = NULL)
    {
        $this->phpExcelObject = $this->createPhpExcelObject();

        $this->phpExcelObject->setActiveSheetIndex(0);

        $this->buildHeader();

        $this->buildReportTable($vendingMachineAmount);

        list($currentRow, $purchaseTotalAmount, $purchaseTotalSum) = $this->buildBody($accountingData);

        $this->buildFooter($currentRow, $purchaseTotalAmount, $purchaseTotalSum);

        $this
            ->adjustCellWidth()
            ->adjustRowHigh()
        ;

        $this->phpExcelObject->getActiveSheet()->setTitle('Итого');

        return $this->phpExcelObject;
    }

    protected function buildHeader()
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 1, "Итоговая карта продаж")
            ->mergeCells($this->getPosition(1, 1))
        ;

        $this
            ->styleAlignHorizontalCenter(1, 1)
            ->styleFontBold(1, 1)
        ;
    }

    protected function buildReportTable($vendingMachineAmount)
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(5, 3, "Дата:")
            ->setCellValueByColumnAndRow(6, 3, (new DateTime)->format('m/d/Y'))
        ;

        $this
            ->styleAlignHorizontalRight(3, 3, 'F')
            ->styleAlignHorizontalRight(3, 3, 'G')
            ->styleBorderThin(3, 3, 'F')
            ->styleBorderThin(3, 3, 'G')
            ->styleFontBold(3, 3, 'F')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(5, 4, "Время:")
            ->setCellValueByColumnAndRow(6, 4, (new DateTime)->format('H:i'))
        ;

        $this
            ->styleAlignHorizontalRight(4, 4, 'F')
            ->styleAlignHorizontalRight(4, 4, 'G')
            ->styleBorderThin(4, 4, 'F')
            ->styleBorderThin(4, 4, 'G')
            ->styleFontBold(4, 4, 'F')
        ;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(5, 5, "Количество автоматов:")
            ->setCellValueByColumnAndRow(6, 5, $vendingMachineAmount)
        ;

        $this
            ->styleAlignHorizontalRight(5, 5, 'F')
            ->styleAlignHorizontalRight(5, 5, 'G')
            ->styleBorderThin(5, 5, 'F')
            ->styleBorderThin(5, 5, 'G')
            ->styleFontBold(5, 5, 'F')
        ;
    }

    protected function buildBody(array $accountingData)
    {
        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 7, "№ п/п")
            ->setCellValueByColumnAndRow(1, 7, "Категория")
            ->setCellValueByColumnAndRow(2, 7, "Артикул")
            ->setCellValueByColumnAndRow(3, 7, "Название товара")
            ->setCellValueByColumnAndRow(4, 7, "Количество")
            ->setCellValueByColumnAndRow(5, 7, "Цена")
            ->setCellValueByColumnAndRow(6, 7, "Стоимость")
        ;

        $this
            ->styleAlignHorizontalCenter(7, 7)
            ->styleBorderThick(7, 7)
            ->styleFontBold(7, 7)
        ;

        $currentRow = 8;

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
                $currentProduct = $purchaseData[0]->getProduct();

                $this->phpExcelObject->getActiveSheet()
                    ->setCellValueByColumnAndRow(0, $currentRow, $currentProduct->getId())
                    ->setCellValueByColumnAndRow(1, $currentRow, $currentProduct->getProductCategory()->getName())
                    ->setCellValueByColumnAndRow(2, $currentRow, $currentProduct->getCode())
                    ->setCellValueByColumnAndRow(3, $currentRow, $currentProduct->getNameFull())
                    ->setCellValueByColumnAndRow(4, $currentRow, $purchaseData['purchaseAmount'])
                    ->setCellValueByColumnAndRow(5, $currentRow, $currentProduct->getPrice())
                    ->setCellValueByColumnAndRow(6, $currentRow, $purchaseData['purchaseSum'])
                ;

                $this->styleBorderThin($currentRow, $currentRow);

                $currentRow++;

                $purchaseTotalCategory = [
                    'amount' => $purchaseTotalCategory['amount'] + $purchaseData['purchaseAmount'],
                    'sum'    => bcadd($purchaseTotalCategory['sum'], $purchaseData['purchaseSum'], 2)
                ];
            }

            $this->phpExcelObject->getActiveSheet()
                ->setCellValueByColumnAndRow(3, $currentRow, "Итого в категории \"{$category}\":")
                ->setCellValueByColumnAndRow(4, $currentRow, $purchaseTotalCategory['amount'])
                ->setCellValueByColumnAndRow(6, $currentRow, $purchaseTotalCategory['sum'])
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

    protected function buildFooter($currentRow, $purchaseTotalAmount, $purchaseTotalSum)
    {
        $currentRow++;

        $this->phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(3, $currentRow, "Итого по всем категориям:")
            ->setCellValueByColumnAndRow(4, $currentRow, $purchaseTotalAmount)
            ->setCellValueByColumnAndRow(6, $currentRow, $purchaseTotalSum)
        ;

        $this
            ->styleBorderThick($currentRow, $currentRow)
            ->styleFillSolid($currentRow, $currentRow, '558ED5')
            ->styleFontBold($currentRow, $currentRow)
        ;
    }
}