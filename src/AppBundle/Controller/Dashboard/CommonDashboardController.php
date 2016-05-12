<?php
// AppBundle/Controller/Dashboard/CommonDashboardController.php
namespace AppBundle\Controller\Dashboard;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Interfaces\PaginatorInterface;

class CommonDashboardController extends Controller implements PaginatorInterface
{
    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.common.paginator") */
    private $_paginator;

    /** @DI\Inject("app.common.search") */
    private $_search;

    /** @DI\Inject("app.common.entity_results_manager") */
    private $_entityResultsManager;

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

    public function paginationAction()
    {
        if( !$this->_paginator->isPaginationRequired() )
            return new Response();

        $pages = [
            'first'   => $this->_paginator->getFirstPage(),
            'last'    => $this->_paginator->getLastPage(),
            'current' => $this->_paginator->getPageArgument()
        ];

        $display = [
            'start' => self::PAGES_RANGE,
            'end'   => $pages['last'] - self::PAGES_RANGE,
            'range' => range(
                $pages['current'] - floor(self::PAGES_RANGE / 2),
                $pages['current'] + floor(self::PAGES_RANGE / 2)
            )
        ];

        return $this->render('AppBundle:Dashboard/Common:pagination.html.twig', [
            '_entityResultsManager' => $this->_entityResultsManager,
            'pages'                 => $pages,
            'display'               => $display
        ]);
    }

    public function searchAction()
    {
        $searchArgument = $this->_search->getAnySearchArgument();

        return $this->render('AppBundle:Dashboard/Common:search.html.twig', [
            'searchArgument' => $searchArgument
        ]);
    }
}
