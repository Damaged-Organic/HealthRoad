<?php
// AppBundle/Controller/Binding/CustomerController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Student\Student,
    AppBundle\Security\Authorization\Voter\CustomerVoter;

class CustomerController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer/update/{objectId}/bounded/{objectClass}",
     *      name="customer_update_bounded",
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

        $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($objectId);

        if( !$customer )
            throw $this->createNotFoundException("Employee identified by `id` {$objectId} not found");

        if( !$this->isGranted(CustomerVoter::CUSTOMER_READ, $customer) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs->add('customer_read')->add('customer_update', ['id' => $objectId], $_translator->trans('customer_bounded', [], 'routes'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $bounded = $this->forward('AppBundle:Binding\Student:show', [
                    'objectClass' => $this->getObjectClassName($customer),
                    'objectId'    => $objectId
                ]);

                $_breadcrumbs->add('customer_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => $objectClass
                    ],
                    $_translator->trans('student_read', [], 'routes')
                );
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/Customer/Binding:bounded.html.twig', [
            'objectClass' => $objectClass,
            'bounded'     => $bounded->getContent(),
            'customer'    => $customer
        ]);
    }
}