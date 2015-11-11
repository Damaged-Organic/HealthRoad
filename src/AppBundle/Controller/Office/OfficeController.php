<?php
// AppBundle/Controller/Office/OfficeController.php
namespace AppBundle\Controller\Office;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OfficeController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office",
     *      name="customer_office",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Office:layout.html.twig');
    }
}