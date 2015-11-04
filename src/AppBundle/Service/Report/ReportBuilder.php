<?php
// AppBundle/Service/Report/ReportBuilder.php
namespace AppBundle\Service\Report;

use DateTime;

use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Product\Product;

class ReportBuilder
{
    private $_manager;

    public function setManager(EntityManager $_manager)
    {
        $this->_manager = $_manager;
    }

    public function getAccountingData()
    {
        $yesterdayDate = (new DateTime)->modify('yesterday')->format('Y-m-d');

        $accountingData = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findGroupedBySchoolByDate($yesterdayDate);

        return $accountingData;
    }

    public function prepareAccountingData($accountingData)
    {
        $preparedAccountingData = [];

        foreach( $accountingData as $purchaseData )
        {
            if( $purchaseData[0]->getProduct() instanceof Product )
            {
                if( $purchaseData[0]->getProduct()->getProductCategory() ) {
                    $preparedAccountingData[$purchaseData[0]->getProduct()->getProductCategory()->getName()][] = $purchaseData;
                } else {
                    $preparedAccountingData['Без категории'][] = $purchaseData;
                }
            }
        }

        return $preparedAccountingData;
    }

    public function getLogisticsData()
    {

    }
}