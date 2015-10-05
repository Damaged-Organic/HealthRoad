<?php
// AppBundle/Controller/CRUD/CustomerController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Customer\Customer,
    AppBundle\Form\Type\CustomerType,
    AppBundle\Security\Authorization\Voter\CustomerVoter,
    AppBundle\Service\Security\CustomerBoundlessAccess;

class CustomerController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer/{id}",
     *      name="customer_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_customerBoundlessAccess = $this->get('app.security.customer_boundless_access');

        if( $id )
        {
            $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($id);

            if( !$customer )
                throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

            if( !$this->isGranted(CustomerVoter::CUSTOMER_READ, $customer) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Customer/CRUD:readItem.html.twig',
                'data' => ['customer' => $customer]
            ];
        } else {
            if( !$_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $customers = $_manager->getRepository('AppBundle:Customer\Customer')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Customer/CRUD:readList.html.twig',
                'data' => ['customers' => $customers]
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/customer/create",
     *      name="customer_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_customerBoundlessAccess = $this->get('app.security.customer_boundless_access');

        if( !$_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $customerType = new CustomerType($_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE));

        $form = $this->createForm($customerType, $customer = new Customer);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            return $this->render('AppBundle:Entity/Customer/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $encodedPassword = $this
                ->container->get('security.password_encoder')
                ->encodePassword($customer, $customer->getPassword())
            ;

            // Set customer's password
            $customer->setPassword($encodedPassword);

            // Set employee who created customer
            $customer->setEmployee($this->getUser());

            $_manager->persist($customer);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('customer_read');
            } else {
                return $this->redirectToRoute('customer_update', [
                    'id' => $customer->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/customer/update/{id}",
     *      name="customer_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_customerBoundlessAccess = $this->get('app.security.customer_boundless_access');

        $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($id);

        if( !$customer )
            throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

        if( !$this->isGranted(CustomerVoter::CUSTOMER_UPDATE, $customer) ) {
            return $this->redirectToRoute('customer_read', [
                'id' => $customer->getId()
            ]);
        }

        $customerType = new CustomerType($_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE));

        $form = $this->createForm($customerType, $customer);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            if( $form->has('password') && $form->get('password')->getData() )
            {
                $encodedPassword = $this
                    ->container->get('security.password_encoder')
                    ->encodePassword($customer, $customer->getPassword());

                $customer->setPassword($encodedPassword);
            }

            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('customer_read');
            } else {
                return $this->redirectToRoute('customer_update', [
                    'id' => $customer->getId()
                ]);
            }
        }

        return $this->render('AppBundle:Entity/Customer/CRUD:updateItem.html.twig', [
            'form'     => $form->createView(),
            'customer' => $customer
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer/delete/{id}",
     *      name="customer_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $customer = $_manager->getRepository('AppBundle:Customer\Customer')->find($id);

        if( !$customer )
            throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

        if( !$this->isGranted(CustomerVoter::CUSTOMER_DELETE, $customer) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($customer);
        $_manager->flush();

        return $this->redirectToRoute('customer_read');
    }
}