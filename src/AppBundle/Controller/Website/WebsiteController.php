<?php
// AppBundle/Controller/Website/WebsiteController.php
namespace AppBundle\Controller\Website;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class WebsiteController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="website_index",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Website/State:index.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/excel",
     *      name="website_excel",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     */
    public function excelAction()
    {
        $_reportBuilder = $this->get('app.report.builder');

        $accountingData = $_reportBuilder->prepareAccountingData($_reportBuilder->getAccountingData());

        // ***

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("liuggio")
            ->setLastModifiedBy("Giulio De Donato")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        $phpExcelObject->setActiveSheetIndex(0);

        $phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(0, 1, "Отчет по продажам сети за торговый день")
            ->mergeCells('A1:H1')
            ->setCellValueByColumnAndRow(6, 2, "Дата:")
            ->setCellValueByColumnAndRow(7, 2, (new \DateTime)->modify('yesterday')->format('m/d/Y'))
            ->setCellValueByColumnAndRow(0, 4, "№ п/п")
            ->setCellValueByColumnAndRow(1, 4, "Дата")
            ->setCellValueByColumnAndRow(2, 4, "Категория")
            ->setCellValueByColumnAndRow(3, 4, "Артикул")
            ->setCellValueByColumnAndRow(4, 4, "Название товара")
            ->setCellValueByColumnAndRow(5, 4, "Количество")
            ->setCellValueByColumnAndRow(6, 4, "Цена")
            ->setCellValueByColumnAndRow(7, 4, "Стоимость")
        ;

        $phpExcelObject->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //borders
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK
                )
            )
        );

        $phpExcelObject->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(TRUE);
        $phpExcelObject->getActiveSheet()->getStyle('G2')->getFont()->setBold(TRUE);

        $phpExcelObject->getActiveSheet()->getStyle('A4:H4')->applyFromArray($styleArray);
        $phpExcelObject->getActiveSheet()->getStyle('A4:H4')->getFont()->setBold(TRUE);

        $row = 5;
        $counter = 1;
        $purchaseTotalAmount = 0;
        $purchaseTotalSum    = 0;

        foreach( $accountingData as $category => $purchases )
        {
            $row++;

            $purchaseAmount = 0;
            $purchaseSum    = 0;

            foreach($purchases as $purchaseData)
            {
                $phpExcelObject->getActiveSheet()
                    ->setCellValueByColumnAndRow(0, $row, $counter)
                    ->setCellValueByColumnAndRow(1, $row, $purchaseData[0]->getSyncPurchasedAt()->format('m/d/Y'))
                    ->setCellValueByColumnAndRow(2, $row, $purchaseData[0]->getProduct()->getProductCategory()->getName())
                    ->setCellValueByColumnAndRow(3, $row, NULL)
                    ->setCellValueByColumnAndRow(4, $row, $purchaseData[0]->getProduct()->getNameFull())
                    ->setCellValueByColumnAndRow(5, $row, $purchaseData['purchaseAmount'])
                    ->setCellValueByColumnAndRow(6, $row, $purchaseData[0]->getProduct()->getPrice())
                    ->setCellValueByColumnAndRow(7, $row, $purchaseData['purchaseSum'])
                ;

                //borders
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    )
                );

                $phpExcelObject->getActiveSheet()->getStyle("A{$row}:H{$row}")->applyFromArray($styleArray);

                $purchaseAmount += $purchaseData['purchaseAmount'];
                $purchaseSum    = bcadd($purchaseSum, $purchaseData['purchaseSum'], 2);

                $row++;

                $counter++;
            }

            $phpExcelObject->getActiveSheet()
                ->setCellValueByColumnAndRow(4, $row, "Итого в категории \"{$category}\"")
                ->setCellValueByColumnAndRow(5, $row, $purchaseAmount)
                ->setCellValueByColumnAndRow(7, $row, $purchaseSum)
            ;

            //borders
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $phpExcelObject->getActiveSheet()->getStyle("A{$row}:H{$row}")->applyFromArray($styleArray);

            //color row
            $phpExcelObject->getActiveSheet()
                ->getStyle("A{$row}:H{$row}")
                ->getFill()
                ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('FFFF00')
            ;

            //font
            $phpExcelObject->getActiveSheet()->getStyle("A{$row}:H{$row}")->getFont()->setBold(TRUE);

            $purchaseTotalAmount += $purchaseAmount;
            $purchaseTotalSum    = bcadd($purchaseTotalSum, $purchaseSum, 2);

            $row++;
        }

        $row++;

        $phpExcelObject->getActiveSheet()
            ->setCellValueByColumnAndRow(4, $row, "Итого по всем категориям")
            ->setCellValueByColumnAndRow(5, $row, $purchaseTotalAmount)
            ->setCellValueByColumnAndRow(7, $row, $purchaseTotalSum)
        ;

        //borders
        $styleArray = array(
            'borders' => array(
                'allborders' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THICK
                )
            )
        );

        $phpExcelObject->getActiveSheet()->getStyle("A{$row}:H{$row}")->applyFromArray($styleArray);

        //color row
        $phpExcelObject->getActiveSheet()
            ->getStyle("A{$row}:H{$row}")
            ->getFill()
            ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('558ED5')
        ;

        //font
        $phpExcelObject->getActiveSheet()->getStyle("A{$row}:H{$row}")->getFont()->setBold(TRUE);

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        //auto-adjust cell width
        foreach (range('A', $phpExcelObject->getActiveSheet()->getHighestDataColumn()) as $col) {
            $phpExcelObject->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        //auto-adjust cell height
        $phpExcelObject->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'stream-file.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}