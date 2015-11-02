<?php
// AppBundle/Controller/Dashboard/CommonDashboardController.php
namespace AppBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommonDashboardController extends Controller
{
    public function breadcrumbsAction()
    {
        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        return $this->render('AppBundle:Dashboard/Common:breadcrumbs.html.twig', [
            'breadcrumbs' => $_breadcrumbs->getBreadcrumbs()
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
        $_globalRepository = $this->get('app.repository.global');

        $quantities = $_globalRepository->countEntities();

        $route = $request->attributes->get('_route');

        $controller = $request->attributes->get('_controller');

        return $this->render('AppBundle:Dashboard/Common:entities.html.twig', [
            'route'      => $route,
            'controller' => $controller,
            'quantities' => $quantities
        ]);
    }

    public function messagesAction()
    {
        $_session = $this->get('session');

        $fillMessages = function($_session)
        {
            $messages = [];

            if( $this->get('session')->getFlashBag()->has('messages') )
            {
                foreach( $_session->getFlashBag()->get('messages') as $messageArray ) {
                    foreach($messageArray as $type => $message) {
                        $messages[$type] = $message;
                    }
                }
            }

            return $messages;
        };

        $messages = $fillMessages($_session);

        return ( $messages )
            ? $this->render('AppBundle:Dashboard/Common:messages.html.twig', [
                  'messages' => $messages
              ])
            : new Response;
    }
}