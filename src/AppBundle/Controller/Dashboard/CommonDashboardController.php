<?php
// AppBundle/Controller/Dashboard/CommonDashboardController.php
namespace AppBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

class CommonDashboardController extends Controller
{
    public function entitiesListAction()
    {
        $_manager = $this->getDoctrine()->getManager();

        $entitiesQuantity = [
            'regions'             => $_manager->getRepository('AppBundle:Region\Region')->count(),
            'employees'           => $_manager->getRepository('AppBundle:Employee\Employee')->count(),
            'settlement'          => $_manager->getRepository('AppBundle:Settlement\Settlement')->count(),
            'school'              => $_manager->getRepository('AppBundle:School\School')->count(),
            'vendingMachine'      => $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->count(),
            'nfcTag'              => $_manager->getRepository('AppBundle:NfcTag\NfcTag')->count(),
            'supplier'            => $_manager->getRepository('AppBundle:Supplier\Supplier')->count(),
            'product'             => $_manager->getRepository('AppBundle:Product\Product')->count(),
            'productVendingGroup' => $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->count(),
            'customer'            => $_manager->getRepository('AppBundle:Customer\Customer')->count(),
            'student'             => $_manager->getRepository('AppBundle:Student\Student')->count(),
        ];

        return $this->render('AppBundle:Dashboard/Common:entitiesList.html.twig', [
            'entitiesQuantity' => $entitiesQuantity
        ]);
    }

    public function breadcrumbsAction(Request $request)
    {
        return $this->render('AppBundle:Dashboard/Common:breadcrumbs.html.twig', [
            'pageTitle' => $this->get('translator')->trans($request->attributes->get('_route'), [], 'routes')
        ]);
    }
}