<?php
// AppBundle/Service/Report/Utility/Extended/ReportExcel.php
namespace AppBundle\Service\Report\Utility\Extended;

use DateTime;

use PHPExcel,
    PHPExcel_Style_Alignment,
    PHPExcel_Style_Border,
    PHPExcel_Style_Fill;

use Liuggio\ExcelBundle\Factory;

class ReportExcel
{
    const COLUMN_START = 'A';
    const COLUMN_END   = 'H';

    const REPORT_DIRECTORY = '/../src/AppBundle/Resources/reports';

    protected $_phpExcel;

    protected $rootDirectory;
    protected $phpExcelObject;

    public function setPhpExcel(Factory $phpExcel)
    {
        $this->_phpExcel = $phpExcel;
    }

    public function setRootDirectory($rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function getRootDirectory()
    {
        return $this->rootDirectory . static::REPORT_DIRECTORY;
    }

    public function createPhpExcelObject()
    {
        return $this->_phpExcel->createPHPExcelObject();
    }

    public function createExcelFilename()
    {
        return (new DateTime)->format('Y-m-d');
    }

    public function savePhpExcelObject(PHPExcel $phpExcelObject)
    {
        $filePath = $this->getRootDirectory() . "/" . $this->createExcelFilename() . ".xls";

        $this->_phpExcel
            ->createWriter($phpExcelObject, 'Excel5')
            ->save($filePath)
        ;

        return $filePath;
    }

    protected function getPosition($rowStart, $rowEnd, $singleColumn = NULL)
    {
        return ( $singleColumn && ($rowStart === $rowEnd) )
            ? $singleColumn . $rowStart
            : static::COLUMN_START . $rowStart . ":" . static::COLUMN_END . $rowEnd
        ;
    }

    protected function styleAlignHorizontalCenter($rowStart, $rowEnd, $singleColumn = NULL)
    {
        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd, $singleColumn))
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        ;

        return $this;
    }

    protected function styleAlignHorizontalRight($rowStart, $rowEnd, $singleColumn = NULL)
    {
        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd, $singleColumn))
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
        ;

        return $this;
    }

    protected function styleFontBold($rowStart, $rowEnd, $singleColumn = NULL)
    {
        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd, $singleColumn))
            ->getFont()
            ->setBold(TRUE)
        ;

        return $this;
    }

    protected function styleBorderThick($rowStart, $rowEnd, $singleColumn = NULL)
    {
        $style = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THICK
                ]
            ]
        ];

        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd, $singleColumn))
            ->applyFromArray($style)
        ;

        return $this;
    }

    protected function styleBorderThin($rowStart, $rowEnd, $singleColumn = NULL)
    {
        $style = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ];

        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd, $singleColumn))
            ->applyFromArray($style)
        ;

        return $this;
    }

    protected function styleFillSolid($rowStart, $rowEnd, $color)
    {
        $this->phpExcelObject->getActiveSheet()
            ->getStyle($this->getPosition($rowStart, $rowEnd))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color)
        ;

        return $this;
    }

    protected function adjustCellWidth()
    {
        foreach( range(static::COLUMN_START, static::COLUMN_END) as $column )
        {
            $this->phpExcelObject->getActiveSheet()
                ->getColumnDimension($column)
                ->setAutoSize(TRUE)
            ;
        }

        return $this;
    }

    protected function adjustRowHigh()
    {
        $this->phpExcelObject->getActiveSheet()
            ->getDefaultRowDimension()
            ->setRowHeight(20)
        ;

        return $this;
    }
}