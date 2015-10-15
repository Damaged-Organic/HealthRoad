<?php
// AppBundle/Controller/Binding/EmployeeController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Region\Region,
    AppBundle\Entity\School\School,
    AppBundle\Security\Authorization\Voter\EmployeeVoter;

class EmployeeController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/employee/update/{objectId}/bounded/{objectClass}",
     *      name="employee_update_bounded",
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

        $employee = $_manager->getRepository('AppBundle:Employee\Employee')->find($objectId);

        if( !$employee )
            throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

        if( !$this->isGranted(EmployeeVoter::EMPLOYEE_READ, $employee) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('employee_read')->add('employee_update', ['id' => $objectId]);

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Region, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Region:show', [
                    'objectClass' => $this->getObjectClassName($employee),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('region_read', [], 'routes')
                );
            break;

            case $this->compareObjectClassNameToString(new School, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\School:show', [
                    'objectClass' => $this->getObjectClassName($employee),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('employee_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('school_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Employee/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'employee'    => $employee
        ]);
    }
}