<?php
// AppBundle/Controller/Binding/SettlementController.php
namespace AppBundle\Controller\Binding;

use AppBundle\Entity\School\School;
use AppBundle\Security\Authorization\Voter\RegionVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SettlementBoundlessAccess,
    AppBundle\Entity\Region\Region;

class SettlementController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_settlementBoundlessAccess = $this->get('app.security.settlement_boundless_access');

        if( !$_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Region, $objectClass):
                $object = $_manager->getRepository('AppBundle:Region\Region')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Region identified by `id` {$objectId} not found");

                $settlements = $_manager->getRepository('AppBundle:Settlement\Settlement')->findBy(['region' => $object]);

                $action = [
                    'path'  => 'settlement_choose',
                    'voter' => RegionVoter::REGION_BIND
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Settlement/Binding:show.html.twig', [
            'standalone'  => TRUE,
            'settlements' => $settlements,
            'object'      => $object,
            'action'      => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/update/{objectId}/bounded/{objectClass}",
     *      name="settlement_update_bounded",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectId" = "\d+", "objectClass" = "[a-z]+"}
     * )
     */
    public function boundedAction($objectId, $objectClass)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $_translator = $this->get('translator');

        $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($objectId);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$objectId} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_READ, $settlement) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('settlement_read')->add('settlement_update', ['id' => $objectId]);

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new School, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\School:show', [
                    'objectClass' => $this->getObjectClassName($settlement),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('settlement_update_bounded',
                    [
                        'objectId' => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('school_read', [], 'routes')
                );
                break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
                break;
        }

        return $this->render('AppBundle:Entity/Settlement/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'settlement'  => $settlement
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/choose_for/{objectClass}/{objectId}",
     *      name="settlement_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        $_regionBoundlessAccess = $this->get('app.security.settlement_boundless_access');

        if( !$_regionBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        $settlements = $_manager->getRepository('AppBundle:Settlement\Settlement')->findAll();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Region, $objectClass):
                $region = $_manager->getRepository('AppBundle:Region\Region')->find($objectId);

                if( !$region )
                    throw $this->createNotFoundException("Region identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($region),
                    'id'    => $region->getId()
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Settlement/Binding:choose.html.twig', [
            'settlements' => $settlements,
            'objectClass' => $object['class'],
            'objectId'    => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/settlement/bind",
     *      name="settlement_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $settlementId = ( $request->request->has('settlementId') ) ? $request->request->get('settlementId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($settlementId);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$settlementId} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_BIND, $settlement) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->has('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->has('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Region, $objectClass):
                $region = $_manager->getRepository('AppBundle:Region\Region')->find($objectId);

                if( !$region )
                    throw $this->createNotFoundException("Region identified by `id` {$objectId} not found");

                $region->addSettlement($settlement);

                $_manager->persist($region);

                $redirect = [
                    'route' => "region_update",
                    'id'    => $region->getId()
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
     *      "/settlement/unbind/{id}/{objectClass}/{objectId}",
     *      name="settlement_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_BIND, $settlement) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Region, $objectClass):
                //this should be gone in AJAX version
                $regionId = $settlement->getRegion()->getId();

                $settlement->setRegion(NULL);

                $redirect = [
                    'route' => "region_update",
                    'id'    => $regionId
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