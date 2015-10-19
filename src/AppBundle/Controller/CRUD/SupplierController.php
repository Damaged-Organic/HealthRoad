<?php
// AppBundle/Controller/CRUD/SupplierController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Supplier\Supplier,
    AppBundle\Form\Type\SupplierType,
    AppBundle\Security\Authorization\Voter\SupplierVoter,
    AppBundle\Service\Security\SupplierBoundlessAccess;

class SupplierController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/supplier/{id}",
     *      name="supplier_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_supplierBoundlessAccess = $this->get('app.security.supplier_boundless_access');

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

            if( !$supplier )
                throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

            if( !$this->isGranted(SupplierVoter::SUPPLIER_READ, $supplier) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Supplier/CRUD:readItem.html.twig',
                'data' => ['supplier' => $supplier]
            ];

            $_breadcrumbs->add('supplier_read')->add('supplier_read', ['id' => $id], $_translator->trans('supplier_view', [], 'routes'));
        } else {
            if( !$_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $suppliers = $_manager->getRepository('AppBundle:Supplier\Supplier')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Supplier/CRUD:readList.html.twig',
                'data' => ['suppliers' => $suppliers]
            ];

            $_breadcrumbs->add('supplier_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/supplier/create",
     *      name="supplier_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_supplierBoundlessAccess = $this->get('app.security.supplier_boundless_access');

        if( !$_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $supplierType = new SupplierType($_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE));

        $form = $this->createForm($supplierType, $supplier = new Supplier, [
            'action' => $this->generateUrl('supplier_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $_breadcrumbs->add('supplier_read')->add('supplier_create');

            return $this->render('AppBundle:Entity/Supplier/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($supplier);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('supplier_read');
            } else {
                return $this->redirectToRoute('supplier_update', [
                    'id' => $supplier->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/supplier/update/{id}",
     *      name="supplier_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_supplierBoundlessAccess = $this->get('app.security.supplier_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

        if( !$supplier )
            throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

        if( !$this->isGranted(SupplierVoter::SUPPLIER_UPDATE, $supplier) ) {
            return $this->redirectToRoute('supplier_read', [
                'id' => $supplier->getId()
            ]);
        }

        $supplierType = new SupplierType($_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE));

        $form = $this->createForm($supplierType, $supplier, [
            'action' => $this->generateUrl('supplier_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('supplier_read');
            } else {
                return $this->redirectToRoute('supplier_update', [
                    'id' => $supplier->getId()
                ]);
            }
        }

        $_breadcrumbs->add('supplier_read')->add('supplier_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Supplier/CRUD:updateItem.html.twig', [
            'form'     => $form->createView(),
            'supplier' => $supplier
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/supplier/delete/{id}",
     *      name="supplier_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $supplier = $_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

        if( !$supplier )
            throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

        if( !$this->isGranted(SupplierVoter::SUPPLIER_DELETE, $supplier) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($supplier);
        $_manager->flush();

        return $this->redirectToRoute('supplier_read');
    }
}