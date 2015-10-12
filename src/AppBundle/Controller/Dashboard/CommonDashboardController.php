<?php
// AppBundle/Controller/Dashboard/CommonDashboardController.php
namespace AppBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

class CommonDashboardController extends Controller
{
    public function breadcrumbsAction()
    {
        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        return $this->render('AppBundle:Dashboard/Common:breadcrumbs.html.twig', [
            'breadcrumbs' => $_breadcrumbs->getBreadcrumbs()
        ]);
    }

    public function toolbarAction()
    {
        return $this->render('AppBundle:Dashboard/Common:toolbar.html.twig');
    }

    public function entitiesAction(Request $request)
    {
        $_globalRepository = $this->get('app.repository.global');

        $quantities = $_globalRepository->countEntities();

        $route = $request->attributes->get('_route');

        return $this->render('AppBundle:Dashboard/Common:entities.html.twig', [
            'route'      => $route,
            'quantities' => $quantities
        ]);
    }
}