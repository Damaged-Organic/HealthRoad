<?php
// AppBundle/Service/Repository/GlobalRepository.php
namespace AppBundle\Service\Repository;

use Doctrine\DBAL\Connection;

class GlobalRepository
{
    private $_connection;

    public function setConnection(Connection $connection)
    {
        $this->_connection = $connection;
    }

    public function countEntities()
    {
        $query = "
            SELECT
              (SELECT COUNT(id) FROM employees) AS employees,
              (SELECT COUNT(id) FROM customers) AS customers,
              (SELECT COUNT(id) FROM students) AS students,
              (SELECT COUNT(id) FROM nfc_tags) AS nfcTags,
              (SELECT COUNT(id) FROM regions) AS regions,
              (SELECT COUNT(id) FROM settlements) AS settlements,
              (SELECT COUNT(id) FROM schools) AS schools,
              (SELECT COUNT(id) FROM vending_machines) AS vendingMachines,
              (SELECT COUNT(id) FROM products_vending_groups) AS productsVendingGroups,
              (SELECT COUNT(id) FROM suppliers) AS suppliers,
              (SELECT COUNT(id) FROM products) AS products,
              (SELECT COUNT(id) FROM purchases) AS purchases,
              (SELECT COUNT(id) FROM payments_receipts) AS paymentsReceipts
        ";

        $statement = $this->_connection->prepare($query);

        $statement->execute();

        return $statement->fetchAll()[0];
    }
}
