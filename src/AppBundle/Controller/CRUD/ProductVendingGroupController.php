<?php
// AppBundle/Controller/CRUD/VendingMachineGroupController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Common\Utility\Exceptions\SearchException,
    AppBundle\Service\Common\Utility\Exceptions\PaginatorException;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Form\Type\ProductVendingGroupType,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter,
    AppBundle\Service\Security\ProductVendingGroupBoundlessAccess;

class ProductVendingGroupController extends Controller implements UserRoleListInterface
{
    use EntityFilter;

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

    /** @DI\Inject("app.security.product_vending_group_boundless_access") */
    private $_productVendingGroupBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product_vending_group/{id}",
     *      name="product_vending_group_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup');

        if( $id )
        {
            $productVendingGroup = $repository->find($id);

            if( !$productVendingGroup )
                throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

            if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_READ, $productVendingGroup) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/ProductVendingGroup/CRUD:readItem.html.twig',
                'data' => ['productVendingGroup' => $productVendingGroup]
            ];

            $this->_breadcrumbs
                ->add('product_vending_group_read')
                ->add('product_vending_group_read', ['id' => $id], $this->_translator->trans('product_vending_group_view', [], 'routes'))
            ;
        } else {
            if( !$this->_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('product_vending_group_read');
            }

            $productVendingGroups = $this->_entityResultsManager->findRecords($repository);

            if( $productVendingGroups === FALSE )
                return $this->redirectToRoute('product_vending_group_read');

            $productVendingGroups = $this->filterDeletedIfNotGranted(
                ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_READ, $productVendingGroups
            );

            $response = [
                'view' => 'AppBundle:Entity/ProductVendingGroup/CRUD:readList.html.twig',
                'data' => ['productVendingGroups' => $productVendingGroups]
            ];

            $this->_breadcrumbs->add('product_vending_group_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product_vending_group/create",
     *      name="product_vending_group_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $productVendingGroupType = new ProductVendingGroupType(
            $this->_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE)
        );

        $form = $this->createForm($productVendingGroupType, $productVendingGroup = new ProductVendingGroup, [
            'action' => $this->generateUrl('product_vending_group_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_create');

            return $this->render('AppBundle:Entity/ProductVendingGroup/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->_manager->persist($productVendingGroup);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_vending_group_read');
            } else {
                return $this->redirectToRoute('product_vending_group_update', [
                    'id' => $productVendingGroup->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/product_vending_group/update/{id}",
     *      name="product_vending_group_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $productVendingGroup = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($id);

        if( !$productVendingGroup )
            throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_UPDATE, $productVendingGroup) ) {
            return $this->redirectToRoute('product_vending_group_read', [
                'id' => $productVendingGroup->getId()
            ]);
        }

        $productVendingGroupType = new ProductVendingGroupType(
            $this->_productVendingGroupBoundlessAccess->isGranted(ProductVendingGroupBoundlessAccess::PRODUCT_VENDING_GROUP_CREATE)
        );

        $form = $this->createForm($productVendingGroupType, $productVendingGroup, [
            'action' => $this->generateUrl('product_vending_group_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('product_vending_group_read');
            } else {
                return $this->redirectToRoute('product_vending_group_update', [
                    'id' => $productVendingGroup->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/ProductVendingGroup/CRUD:updateItem.html.twig', [
            'form'                => $form->createView(),
            'productVendingGroup' => $productVendingGroup
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/product_vending_group/delete/{id}",
     *      name="product_vending_group_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $productVendingGroup = $this->_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($id);

        if( !$productVendingGroup )
            throw $this->createNotFoundException("Product Vending Group identified by `id` {$id} not found");

        if( !$this->isGranted(ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_DELETE, $productVendingGroup) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$productVendingGroup->getPseudoDeleted() )
        {
            $productVendingGroup->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $productVendingGroup->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return new RedirectResponse($request->headers->get('referer'));
    }
}
