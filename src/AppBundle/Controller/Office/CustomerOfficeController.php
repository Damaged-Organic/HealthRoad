<?php
// AppBundle/Controller/Office/CustomerOfficeController.php
namespace AppBundle\Controller\Office;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomerOfficeController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office",
     *      name="customer_office",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Office:layout.html.twig');
    }
}