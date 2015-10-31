<?php
// AppBundle/Controller/Website/WebsiteController.php
namespace AppBundle\Controller\Website;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Website/State:index.html.twig');
    }
}