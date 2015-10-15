<?php
// AppBundle/Controller/Binding/RegionController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Entity\Employee\Employee,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Security\Authorization\Voter\EmployeeVoter,
    AppBundle\Security\Authorization\Voter\RegionVoter,
    AppBundle\Service\Security\RegionBoundlessAccess;

class RegionController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_regionBoundlessAccess = $this->get('app.security.region_boundless_access');

        if( !$_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $object = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                $regions = $_manager->getRepository('AppBundle:Region\Region')->findBy(['employee' => $object]);

                $action = [
                    'path'  => 'region_choose',
                    'voter' => EmployeeVoter::EMPLOYEE_BIND_REGION
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Region/Binding:show.html.twig', [
            'standalone' => TRUE,
            'regions'    => $regions,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/update/{objectId}/bounded/{objectClass}",
     *      name="region_update_bounded",
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

        $region = $_manager->getRepository('AppBundle:Region\Region')->find($objectId);

        if( !$region )
            throw $this->createNotFoundException("Region identified by `id` {$objectId} not found");

        if( !$this->isGranted(RegionVoter::REGION_READ, $region) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('region_read')->add('region_update', ['id' => $objectId]);

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Settlement, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Settlement:show', [
                    'objectClass' => $this->getObjectClassName($region),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('region_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('settlement_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Region/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'region'      => $region
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/choose_for/{objectClass}/{objectId}",
     *      name="region_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        $_regionBoundlessAccess = $this->get('app.security.region_boundless_access');

        if( !$_regionBoundlessAccess->isGranted(RegionBoundlessAccess::REGION_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $object = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_REGION, $employee) )
                    throw $this->createAccessDeniedException('Access denied: Regions can be bound to manager only');

                $path = 'employee_update_bounded';

                $_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $objectId])->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'region'
                    ],
                    $_translator->trans('region_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $regions = $_manager->getRepository('AppBundle:Region\Region')->findAll();

        $_breadcrumbs->add('region_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);

        return $this->render('AppBundle:Entity/Region/Binding:choose.html.twig', [
            'path'    => $path,
            'regions' => $regions,
            'object'  => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/region/bind/{targetId}/{objectClass}/{objectId}",
     *      name="region_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $region = $_manager->getRepository('AppBundle:Region\Region')->find($targetId);

        if( !$region )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(RegionVoter::REGION_BIND, $region) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

                if( !$employee )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                if( !$this->isGranted(EmployeeVoter::EMPLOYEE_BIND_REGION, $employee) )
                    throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

                $employee->addRegion($region);

                $_manager->persist($employee);
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
     *      "/region/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="region_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_translator = $this->get('translator');

        $region = $_manager->getRepository('AppBundle:Region\Region')->find($targetId);

        if( !$region )
            throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(RegionVoter::REGION_BIND, $region) )
            throw $this->createAccessDeniedException($_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Employee, $objectClass):
                $region->setEmployee(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $_manager->flush();

        return new RedirectResponse($request->headers->get('referer'));
    }
}