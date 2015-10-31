<?php
// AppBundle/Controller/Security/CustomerAccessController.php
namespace AppBundle\Controller\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CustomerAccessController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office/login",
     *      name="customer_office_login",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     */
    public function loginAction()
    {
        if( $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return $this->redirectToRoute('customer_office');
        }

        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('AppBundle:Office/Security/Login:customer.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/customer_office/login_check",
     *      name="customer_office_login_check",
     *      host="{domain}",
     *      defaults={"_locale" = "%locale%", "domain" = "%domain%"},
     *      requirements={"_locale" = "%locale%", "domain" = "%domain%"}
     * )
     */
    public function loginCheckAction()
    {
        // This controller will not be executed, as the route is handled by the Security system
    }
}