<?php
// AppBundle/Controller/CRUD/VendingMachineController.php
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
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Form\Type\VendingMachineType,
    AppBundle\Security\Authorization\Voter\VendingMachineVoter,
    AppBundle\Service\Security\VendingMachineBoundlessAccess;

class VendingMachineController extends Controller implements UserRoleListInterface
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

    /** @DI\Inject("app.security.vending_machine_boundless_access") */
    private $_vendingMachineBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/{id}",
     *      name="vending_machine_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $repository = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine');

        if( $id )
        {
            $vendingMachine = $repository->find($id);

            if( !$vendingMachine )
                throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

            if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_READ, $vendingMachine) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/VendingMachine/CRUD:readItem.html.twig',
                'data' => ['vendingMachine' => $vendingMachine]
            ];

            $this->_breadcrumbs->add('vending_machine_read')->add('vending_machine_read', ['id' => $id], $this->_translator->trans('vending_machine_view', [], 'routes'));
        } else {
            if( !$this->_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            try {
                $this->_entityResultsManager
                    ->setPageArgument($this->_paginator->getPageArgument())
                    ->setSearchArgument($this->_search->getSearchArgument())
                ;
            } catch(PaginatorException $ex) {
                throw $this->createNotFoundException('Invalid page argument');
            } catch(SearchException $ex) {
                return $this->redirectToRoute('vending_machine_read');
            }

            $vendingMachines = $this->_entityResultsManager->findRecords($repository);

            if( $vendingMachines === FALSE )
                return $this->redirectToRoute('vending_machine_read');

            $vendingMachines = $this->filterDeletedIfNotGranted(
                VendingMachineVoter::VENDING_MACHINE_READ, $vendingMachines
            );

            $response = [
                'view' => 'AppBundle:Entity/VendingMachine/CRUD:readList.html.twig',
                'data' => ['vendingMachines' => $vendingMachines]
            ];

            $this->_breadcrumbs->add('vending_machine_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/vending_machine/create",
     *      name="vending_machine_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $vendingMachineType = new VendingMachineType($this->_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE));

        $form = $this->createForm($vendingMachineType, $vendingMachine = new VendingMachine, [
            'action' => $this->generateUrl('vending_machine_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('vending_machine_read')->add('vending_machine_create');

            return $this->render('AppBundle:Entity/VendingMachine/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $encodedPassword = $this
                ->get('app.sync.security.password_encoder')
                ->encodePassword($vendingMachine->getPassword())
            ;

            $vendingMachine->setPassword($encodedPassword);

            $this->_manager->persist($vendingMachine);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('vending_machine_read');
            } else {
                return $this->redirectToRoute('vending_machine_update', [
                    'id' => $vendingMachine->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/vending_machine/update/{id}",
     *      name="vending_machine_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_UPDATE, $vendingMachine) ) {
            return $this->redirectToRoute('vending_machine_read', [
                'id' => $vendingMachine->getId()
            ]);
        }

        $vendingMachineType = new VendingMachineType($this->_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE));

        $form = $this->createForm($vendingMachineType, $vendingMachine, [
            'action' => $this->generateUrl('vending_machine_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            if( $form->has('password') && $form->get('password')->getData() )
            {
                $encodedPassword = $this
                    ->get('app.sync.security.password_encoder')
                    ->encodePassword($vendingMachine->getPassword())
                ;

                $vendingMachine->setPassword($encodedPassword);
            }

            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('vending_machine_read');
            } else {
                return $this->redirectToRoute('vending_machine_update', [
                    'id' => $vendingMachine->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('vending_machine_read')->add('vending_machine_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/VendingMachine/CRUD:updateItem.html.twig', [
            'form'           => $form->createView(),
            'vendingMachine' => $vendingMachine
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/delete/{id}",
     *      name="vending_machine_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $vendingMachine = $this->_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_DELETE, $vendingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$vendingMachine->getPseudoDeleted() )
        {
            $vendingMachine->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $vendingMachine->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return $this->redirectToRoute('vending_machine_read');
    }
}
