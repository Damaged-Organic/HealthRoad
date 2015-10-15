<?php
// AppBundle/Controller/CRUD/VendingMachineController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Form\Type\VendingMachineType,
    AppBundle\Security\Authorization\Voter\VendingMachineVoter,
    AppBundle\Service\Security\VendingMachineBoundlessAccess;

class VendingMachineController extends Controller implements UserRoleListInterface
{
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
        $_manager = $this->getDoctrine()->getManager();

        $_vendingMachineBoundlessAccess = $this->get('app.security.vending_machine_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

            if( !$vendingMachine )
                throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

            if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_READ, $vendingMachine) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/VendingMachine/CRUD:readItem.html.twig',
                'data' => ['vendingMachine' => $vendingMachine]
            ];
        } else {
            if( !$_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/VendingMachine/CRUD:readList.html.twig',
                'data' => ['vendingMachines' => $vendingMachines]
            ];
        }

        $_breadcrumbs->add('vending_machine_read');

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
        $_vendingMachineBoundlessAccess = $this->get('app.security.vending_machine_boundless_access');

        if( !$_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $vendingMachineType = new VendingMachineType($_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE));

        $form = $this->createForm($vendingMachineType, $vendingMachine = new VendingMachine, [
            'action' => $this->generateUrl('vending_machine_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $_breadcrumbs->add('vending_machine_read')->add('vending_machine_create');

            return $this->render('AppBundle:Entity/VendingMachine/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $encodedPassword = $this
                ->get('app.sync.security.password_encoder')
                ->encodePassword($vendingMachine->getPassword())
            ;

            $vendingMachine->setPassword($encodedPassword);

            $_manager->persist($vendingMachine);
            $_manager->flush();

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
        $_manager = $this->getDoctrine()->getManager();

        $_vendingMachineBoundlessAccess = $this->get('app.security.vending_machine_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_UPDATE, $vendingMachine) ) {
            return $this->redirectToRoute('vending_machine_read', [
                'id' => $vendingMachine->getId()
            ]);
        }

        $vendingMachineType = new VendingMachineType($_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_CREATE));

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

            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('vending_machine_read');
            } else {
                return $this->redirectToRoute('vending_machine_update', [
                    'id' => $vendingMachine->getId()
                ]);
            }
        }

        $_breadcrumbs->add('vending_machine_read')->add('vending_machine_update', ['id' => $id]);

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
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_DELETE, $vendingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($vendingMachine);
        $_manager->flush();

        return $this->redirectToRoute('vending_machine_read');
    }
}