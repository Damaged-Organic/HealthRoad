<?php
// AppBundle/Controller/CRUD/SupplierController.php
namespace AppBundle\Controller\CRUD;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Supplier\Supplier,
    AppBundle\Entity\Supplier\SupplierImage,
    AppBundle\Form\Type\SupplierType,
    AppBundle\Security\Authorization\Voter\SupplierVoter,
    AppBundle\Service\Security\SupplierBoundlessAccess;

class SupplierController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

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

    /** @DI\Inject("app.security.supplier_boundless_access") */
    private $_supplierBoundlessAccess;

    /** @DI\Inject("app.validator.uploaded_supplier_image") */
    private $_uploadedSupplierImageValidator;

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
        $repository = $this->_manager->getRepository('AppBundle:Supplier\Supplier');

        if( $id )
        {
            $supplier = $repository->find($id);

            if( !$supplier )
                throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

            if( !$this->isGranted(SupplierVoter::SUPPLIER_READ, $supplier) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Supplier/CRUD:readItem.html.twig',
                'data' => ['supplier' => $supplier]
            ];

            $this->_breadcrumbs->add('supplier_read')->add('supplier_read', ['id' => $id], $this->_translator->trans('supplier_view', [], 'routes'));
        } else {
            if( !$this->_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('supplier_read');
            }

            $suppliers = $this->_entityResultsManager->findRecords($repository);

            if( $suppliers === FALSE )
                return $this->redirectToRoute('supplier_read');

            $response = [
                'view' => 'AppBundle:Entity/Supplier/CRUD:readList.html.twig',
                'data' => ['suppliers' => $suppliers]
            ];

            $this->_breadcrumbs->add('supplier_read');
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
        if( !$this->_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $supplierType = new SupplierType($this->_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE));

        $form = $this->createForm($supplierType, $supplier = new Supplier, [
            'validation_groups' => ['Supplier', 'Strict', 'Create'],
            'action'            => $this->generateUrl('supplier_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid() && $this->_uploadedSupplierImageValidator->validate($form)) ) {
            $this->_breadcrumbs->add('supplier_read')->add('supplier_create');

            return $this->render('AppBundle:Entity/Supplier/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            // TODO: This large logic does not belong to Controller
            if( array_filter($form->getData()->getUploadedSupplierImages()) )
            {
                foreach ($form->getData()->getUploadedSupplierImages() as $uploadedSupplierImage)
                {
                    $supplierImage = (new SupplierImage)
                        ->setImageSupplierFile($uploadedSupplierImage)
                        ->setUpdatedAt(new DateTime)
                    ;

                    $supplier->addSupplierImage($supplierImage);

                    $this->_manager->persist($supplierImage);
                }

                $this->_manager->persist($supplier);
            }

            $this->_manager->persist($supplier);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

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
        $supplier = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

        if( !$supplier )
            throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

        if( !$this->isGranted(SupplierVoter::SUPPLIER_UPDATE, $supplier) ) {
            return $this->redirectToRoute('supplier_read', [
                'id' => $supplier->getId()
            ]);
        }

        $supplierType = new SupplierType($this->_supplierBoundlessAccess->isGranted(SupplierBoundlessAccess::SUPPLIER_CREATE));

        $form = $this->createForm($supplierType, $supplier, [
            'validation_groups' => ['Supplier', 'Strict', 'Update'],
            'action'            => $this->generateUrl('supplier_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() && $this->_uploadedSupplierImageValidator->validate($form) )
        {
            // TODO: This large logic does not belong to Controller
            if( array_filter($form->getData()->getUploadedSupplierImages()) )
            {
                foreach( $supplier->getSupplierImages() as $supplierImage )
                {
                    $supplier->removeSupplierImage($supplierImage);

                    $this->_manager->remove($supplierImage);
                }

                foreach ($form->getData()->getUploadedSupplierImages() as $uploadedSupplierImage)
                {
                    $supplierImage = (new SupplierImage)
                        ->setImageSupplierFile($uploadedSupplierImage)
                        ->setUpdatedAt(new DateTime)
                    ;

                    $supplier->addSupplierImage($supplierImage);

                    $this->_manager->persist($supplierImage);
                }

                $this->_manager->persist($supplier);
            }

            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('supplier_read');
            } else {
                return $this->redirectToRoute('supplier_update', [
                    'id' => $supplier->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('supplier_read')->add('supplier_update', ['id' => $id]);

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
        $supplier = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

        if( !$supplier )
            throw $this->createNotFoundException("Supplier identified by `id` {$id} not found");

        if( !$this->isGranted(SupplierVoter::SUPPLIER_DELETE, $supplier) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($supplier);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return new RedirectResponse($request->headers->get('referer'));
    }
}
