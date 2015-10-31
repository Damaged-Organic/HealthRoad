<?php
// AppBundle/Controller/Binding/VendingMachineController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\School\School,
    AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Entity\Purchase\Purchase,
    AppBundle\Entity\VendingMachine\VendingMachineEvent,
    AppBundle\Entity\VendingMachine\VendingMachineLoad,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Security\Authorization\Voter\VendingMachineVoter,
    AppBundle\Security\Authorization\Voter\SchoolVoter,
    AppBundle\Security\Authorization\Voter\ProductVendingGroupVoter,
    AppBundle\Service\Security\VendingMachineBoundlessAccess;

class VendingMachineController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_vendingMachineBoundlessAccess = $this->get('app.security.vending_machine_boundless_access');

        if( !$_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $object = $_manager->getRepository('AppBundle:School\School')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findBy(['school' => $object]);

                $action = [
                    'path'  => 'vending_machine_choose',
                    'voter' => SchoolVoter::SCHOOL_BIND
                ];
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $object = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findBy(['productVendingGroup' => $object]);

                $action = [
                    'path'  => 'vending_machine_choose',
                    'voter' => ProductVendingGroupVoter::PRODUCT_VENDING_GROUP_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/VendingMachine/Binding:show.html.twig', [
            'standalone'      => TRUE,
            'vendingMachines' => $vendingMachines,
            'object'          => $object,
            'action'          => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/update/{objectId}/bounded/{objectClass}",
     *      name="vending_machine_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_READ, $vendingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('vending_machine_read')->add('vending_machine_update', ['id' => $objectId], $_translator->trans('vending_machine_bounded', [], 'routes'));

        switch(TRUE)
        {

            /*case $this->compareObjectClassNameToString(new NfcTag, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\NfcTag:show', [
                    'objectClass' => $this->getObjectClassName($vendingMachine),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('vending_machine_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('nfc_tag_read', [], 'routes')
                );
            break;*/

            case $this->compareObjectClassNameToString(new Purchase, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Purchase:show', [
                    'objectClass' => $this->getObjectClassName($vendingMachine),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('vending_machine_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('purchase_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new VendingMachineEvent, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\VendingMachineEvent:show', [
                    'objectClass' => $this->getObjectClassName($vendingMachine),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('vending_machine_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('vending_machine_event_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new VendingMachineLoad, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\VendingMachineLoad:show', [
                    'objectClass' => $this->getObjectClassName($vendingMachine),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('vending_machine_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('vending_machine_load_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/VendingMachine/Binding:bounded.html.twig', [
            'objectClass'    => $objectClass,
            'bounded'        => $bounded->getContent(),
            'vendingMachine' => $vendingMachine
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/choose_for/{objectClass}/{objectId}",
     *      name="vending_machine_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        $_vendingMachineBoundlessAccess = $this->get('app.security.vending_machine_boundless_access');

        if( !$_vendingMachineBoundlessAccess->isGranted(VendingMachineBoundlessAccess::VENDING_MACHINE_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $school = $object = $_manager->getRepository('AppBundle:School\School')->find($objectId);

                if( !$school )
                    throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

                $path = 'school_update_bounded';

                $_breadcrumbs->add('school_read')->add('school_update', ['id' => $objectId])->add('school_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'vendingmachine'
                    ],
                    $_translator->trans('vending_machine_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $object = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $path = 'product_vending_group_update_bounded';

                $_breadcrumbs->add('product_vending_group_read')->add('product_vending_group_update', ['id' => $objectId])->add('product_vending_group_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'vendingmachine'
                    ],
                    $_translator->trans('vending_machine_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findAll();

        $_breadcrumbs->add('vending_machine_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);

        return $this->render('AppBundle:Entity/VendingMachine/Binding:choose.html.twig', [
            'path'            => $path,
            'vendingMachines' => $vendingMachines,
            'object'          => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/bind/{targetId}/{objectClass}/{objectId}",
     *      name="vending_machine_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($targetId);

        if( !$vendingMachine )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_BIND, $vendingMachine) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $school = $_manager->getRepository('AppBundle:School\School')->find($objectId);

                if( !$school )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $school->addVendingMachine($vendingMachine);

                $_manager->persist($school);
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $productVendingGroup->addVendingMachine($vendingMachine);

                $_manager->persist($productVendingGroup);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="vending_machine_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($targetId);

        if( !$vendingMachine )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_BIND, $vendingMachine) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $vendingMachine->setSchool(NULL);
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $vendingMachine->setProductVendingGroup(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }
}