<?php
// AppBundle/Controller/Office/CommonOfficeController.php
namespace AppBundle\Controller\Office;

use Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Entity\Website\Contact\Contact;

class CommonOfficeController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    public function breadcrumbsAction()
    {
        return $this->render('AppBundle:Office/Common:breadcrumbs.html.twig', [
            'breadcrumbs' => $this->_breadcrumbs->getBreadcrumbs()
        ]);
    }

    public function headerAction()
    {
        $contacts = $this->_manager->getRepository('AppBundle:Website\Contact\Contact')->findBy([
            'alias' => ['support_center', 'support_center_mts', 'support_center_kyivstar', 'support_center_life']
        ]);

        if( !$contacts )
            throw $this->createNotFoundException();

        return $this->render('AppBundle:Office/Common:header.html.twig', [
            'contacts' => Contact::headerize($contacts)
        ]);
    }
}