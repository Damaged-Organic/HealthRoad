<?php
// AppBundle/Controller/Binding/VendingMachineController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\School\School,
    AppBundle\Entity\Product\ProductVendingGroup,
    AppBundle\Security\Authorization\Voter\VendingMachineVoter,
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
                $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findBy(['school' => $objectId]);
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findBy(['productVendingGroup' => $objectId]);
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/VendingMachine/Binding:show.html.twig', [
            'vendingMachines' => $vendingMachines,
            'objectId'        => $objectId,
            'objectClass'     => $objectClass
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

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $school = $_manager->getRepository('AppBundle:School\School')->find($objectId);

                if( !$school )
                    throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($school),
                    'id'    => $school->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($productVendingGroup),
                    'id'    => $productVendingGroup->getId()
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $vendingMachines = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->findAll();

        return $this->render('AppBundle:Entity/VendingMachine/Binding:choose.html.twig', [
            'vendingMachines' => $vendingMachines,
            'objectClass'     => $object['class'],
            'objectId'        => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/vending_machine/bind",
     *      name="vending_machine_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $vendingMachineId = ( $request->request->has('vendingMachineId') ) ? $request->request->get('vendingMachineId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($vendingMachineId);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$vendingMachineId} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_BIND, $vendingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->get('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->get('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $school = $_manager->getRepository('AppBundle:School\School')->find($objectId);

                if( !$school )
                    throw $this->createNotFoundException("School identified by `id` {$objectId} not found");

                $school->addVendingMachine($vendingMachine);

                $_manager->persist($school);

                $redirect = [
                    'route' => "school_update",
                    'id'    => $school->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                $productVendingGroup = $_manager->getRepository('AppBundle:Product\ProductVendingGroup')->find($objectId);

                if( !$productVendingGroup )
                    throw $this->createNotFoundException("Product Vending Group identified by `id` {$objectId} not found");

                $productVendingGroup->addVendingMachine($vendingMachine);

                $_manager->persist($productVendingGroup);

                $redirect = [
                    'route' => "product_vending_group_update",
                    'id'    => $productVendingGroup->getId()
                ];
            break;

            default:
                throw $this->createNotFoundException("Object not supported");
            break;
        }

        $_manager->flush();

        return $this->redirectToRoute($redirect['route'], [
            'id' => $redirect['id']
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/vending_machine/unbind/{id}/{objectClass}/{objectId}",
     *      name="vending_machine_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($id);

        if( !$vendingMachine )
            throw $this->createNotFoundException("Vending Machine identified by `id` {$id} not found");

        if( !$this->isGranted(VendingMachineVoter::VENDING_MACHINE_BIND, $vendingMachine) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                //this should be gone in AJAX version
                $schoolId = $vendingMachine->getSchool()->getId();

                $vendingMachine->setSchool(NULL);

                $redirect = [
                    'route' => "school_update",
                    'id'    => $schoolId
                ];
            break;

            case $this->compareObjectClassNameToString(new ProductVendingGroup, $objectClass):
                //this should be gone in AJAX version
                $productVendingGroupId = $vendingMachine->getProductVendingGroup()->getId();

                $vendingMachine->setProductVendingGroup(NULL);

                $redirect = [
                    'route' => "product_vending_group_update",
                    'id'    => $productVendingGroupId
                ];
            break;

            default:
                throw $this->createNotFoundException("Object not supported");
            break;
        }

        $_manager->flush();

        return $this->redirectToRoute($redirect['route'], [
            'id' => $redirect['id']
        ]);
    }
}