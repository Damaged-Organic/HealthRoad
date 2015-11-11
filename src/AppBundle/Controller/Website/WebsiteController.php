<?php
// AppBundle/Controller/Website/WebsiteController.php
namespace AppBundle\Controller\Website;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class WebsiteController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="website_index",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Website/State:index.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project",
     *      name="website_our_project",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function ourProjectAction()
    {
        return $this->render('AppBundle:Website/State:ourProject.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project/how_to_get_card",
     *      name="website_how_to_get_card",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToGetCardAction()
    {
        return $this->render('AppBundle:Website/State:ourProject.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project/how_to_replenish_card",
     *      name="website_how_to_replenish_card",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToReplenishCardAction()
    {
        return $this->render('AppBundle:Website/State:ourProject.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project/how_to_use_vending_machine",
     *      name="website_how_to_use_vending_machine",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToUseVendingMachineAction()
    {
        return $this->render('AppBundle:Website/State:ourProject.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/about_company",
     *      name="website_about_company",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function aboutCompanyAction()
    {
        return $this->render('AppBundle:Website/State:aboutCompany.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_partners",
     *      name="website_our_partners",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function ourPartnersAction()
    {
        return $this->render('AppBundle:Website/State:ourPartners.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/products",
     *      name="website_products",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function productsAction()
    {
        return $this->render('AppBundle:Website/State:products.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/contacts",
     *      name="website_contacts",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function contactsAction()
    {
        return $this->render('AppBundle:Website/State:contacts.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/contacts/vending_machines_placement",
     *      name="website_vending_machines_placement",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function vendingMachinesPlacementAction()
    {
        return $this->render('AppBundle:Website/State:contacts.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/contacts/vending_machines_suppliers",
     *      name="website_vending_machines_suppliers",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale%", "domain_website" = "%domain_website%"}
     * )
     */
    public function vendingMachinesSuppliersAction()
    {
        return $this->render('AppBundle:Website/State:contacts.html.twig');
    }
}