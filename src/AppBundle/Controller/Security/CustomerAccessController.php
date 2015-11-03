<?php
// AppBundle/Controller/Security/CustomerAccessController.php
namespace AppBundle\Controller\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class CustomerAccessController extends Controller
{
    /** @DI\Inject("security.authorization_checker") */
    private $_authorizationChecker;

    /** @DI\Inject("security.authentication_utils") */
    private $_authenticationUtils;

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
        if( $this->_authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return $this->redirectToRoute('customer_office');
        }

        $error = $this->_authenticationUtils->getLastAuthenticationError();

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