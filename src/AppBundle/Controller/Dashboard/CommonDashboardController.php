<?php
// AppBundle/Controller/Dashboard/CommonDashboardController.php
namespace AppBundle\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class CommonDashboardController extends Controller
{
    /** @DI\Inject("session") */
    private $_session;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.repository.global") */
    private $_globalRepository;

    public function breadcrumbsAction()
    {
        return $this->render('AppBundle:Dashboard/Common:breadcrumbs.html.twig', [
            'breadcrumbs' => $this->_breadcrumbs->getBreadcrumbs()
        ]);
    }

    public function toolbarAction(Request $request)
    {
        $controller = $request->attributes->get('_controller');

        return $this->render('AppBundle:Dashboard/Common:toolbar.html.twig', [
            'controller' => $controller
        ]);
    }

    public function entitiesAction(Request $request)
    {
        $attributes = [
            'route'      => $request->attributes->get('_route'),
            'controller' => $request->attributes->get('_controller')
        ];

        $quantities = $this->_globalRepository->countEntities();

        return $this->render('AppBundle:Dashboard/Common:entities.html.twig', [
            'route'      => $attributes['route'],
            'controller' => $attributes['controller'],
            'quantities' => $quantities
        ]);
    }

    public function messagesAction()
    {
        $messages = $this->_messages->getMessages();

        if( !$messages )
            new Response;

        return $this->render('AppBundle:Dashboard/Common:messages.html.twig', [
            'messages' => $messages
        ]);
    }
}