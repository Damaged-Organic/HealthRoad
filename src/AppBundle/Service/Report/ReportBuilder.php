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

        $accountingData = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findByDateGrouped($yesterdayDate);

        return $accountingData;
    }

    public function getLogisticsAccountingData()
    {
        $accountingData = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findByLoadDateGrouped();

        return $accountingData;
    }

    public function getLogisticsData()
    {
        $vendingMachineReportSumAmountSetting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findVendingMachineReportSumAmount();

        //TODO: Order of the next 2 queries (order in code, no shit) is important. Doctrine completely looses it's fucking mind.

        $logisticsData = [
            'secondary' => $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findReadyByLoadDate(),
            'primary'   => $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findReadyByPurchaseSum($vendingMachineReportSumAmountSetting)
        ];

        return [$logisticsData['primary'], $logisticsData['secondary']];
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

    public function prepareLogisticsData($readyByPurchaseSum, $readyByLoadDate)
    {
        $preparedLogisticsData = [];

        foreach( $readyByPurchaseSum as $vendingMachine )
        {
            $purchases = [];

            foreach( $readyByLoadDate[$vendingMachine[0]->getId()]->getPurchases() as $purchase)
            {
                if( $purchase->getProduct() instanceof Product )
                {
                    $quantity = ( !empty($purchases[$purchase->getProduct()->getId()]) ) ? ++$purchases[$purchase->getProduct()->getId()]['quantity'] : 1;

                    $purchases[$purchase->getProduct()->getId()] = [
                        'object'   => $purchase,
                        'quantity' => $quantity
                    ];
                }
            }

            $preparedLogisticsData[] = [
                'object' => $readyByLoadDate[$vendingMachine[0]->getId()]->setPurchases($purchases),
                'sum'    => $vendingMachine['purchaseSum']
            ];
        }

        return $preparedLogisticsData;
    }
}
