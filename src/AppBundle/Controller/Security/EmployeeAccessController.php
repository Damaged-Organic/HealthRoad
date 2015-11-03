<?php
// AppBundle/Controller/Security/EmployeeAccessController.php
namespace AppBundle\Controller\Security;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class EmployeeAccessController extends Controller
{
    /** @DI\Inject("security.authorization_checker") */
    private $_authorizationChecker;

    /** @DI\Inject("security.authentication_utils") */
    private $_authenticationUtils;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/login",
     *      name="employee_dashboard_login",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function loginAction()
    {
        if( $this->_authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') ) {
            return $this->redirectToRoute('employee_dashboard');
        }

        $error = $this->_authenticationUtils->getLastAuthenticationError();

        return $this->render('AppBundle:Dashboard/Security/Login:employee.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/login_check",
     *      name="employee_dashboard_login_check",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function loginCheckAction()
    {
        // This controller will not be executed, as the route is handled by the Security system
    }
}