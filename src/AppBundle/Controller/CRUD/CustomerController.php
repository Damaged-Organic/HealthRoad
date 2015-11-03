<?php
// AppBundle/Controller/CRUD/CustomerController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Customer\Customer,
    AppBundle\Form\Type\CustomerType,
    AppBundle\Security\Authorization\Voter\CustomerVoter,
    AppBundle\Service\Security\CustomerBoundlessAccess;

class CustomerController extends Controller implements UserRoleListInterface
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.security.customer_boundless_access") */
    private $_customerBoundlessAccess;

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
    public function readAction($id = NULL)
    {
        if( $id )
        {
            $customer = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($id);

            if( !$customer )
                throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

            if( !$this->isGranted(CustomerVoter::CUSTOMER_READ, $customer) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Customer/CRUD:readItem.html.twig',
                'data' => ['customer' => $customer]
            ];

            $this->_breadcrumbs->add('customer_read')->add('customer_read', ['id' => $id], $this->_translator->trans('customer_view', [], 'routes'));
        } else {
            if( !$this->_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $customers = $this->_manager->getRepository('AppBundle:Customer\Customer')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Customer/CRUD:readList.html.twig',
                'data' => ['customers' => $customers]
            ];

            $this->_breadcrumbs->add('customer_read');
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
        if( !$this->_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $customerType = new CustomerType(
            $this->_translator,
            $this->_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE));

        $form = $this->createForm($customerType, $customer = new Customer, [
            'validation_groups' => ['Customer', 'Strict', 'Create'],
            'action'            => $this->generateUrl('customer_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('customer_read')->add('customer_create');

            return $this->render('AppBundle:Entity/Customer/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $encodedPassword = $this
                ->container->get('security.password_encoder')
                ->encodePassword($customer, $customer->getPassword())
            ;

            // Set customer's password
            $customer->setPassword($encodedPassword);

            // Set employee who created customer
            $customer->setEmployee($this->getUser());

            $this->_manager->persist($customer);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

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
        $customer = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($id);

        if( !$customer )
            throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

        if( !$this->isGranted(CustomerVoter::CUSTOMER_UPDATE, $customer) ) {
            return $this->redirectToRoute('customer_read', [
                'id' => $customer->getId()
            ]);
        }

        $customerType = new CustomerType(
            $this->_translator,
            $this->_customerBoundlessAccess->isGranted(CustomerBoundlessAccess::CUSTOMER_CREATE)
        );

        $form = $this->createForm($customerType, $customer, [
            'validation_groups' => ['Customer', 'Strict', 'Update'],
            'action'            => $this->generateUrl('customer_update', ['id' => $id])
        ]);

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

            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('customer_read');
            } else {
                return $this->redirectToRoute('customer_update', [
                    'id' => $customer->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('customer_read')->add('customer_update', ['id' => $id]);

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
        $customer = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($id);

        if( !$customer )
            throw $this->createNotFoundException("Customer identified by `id` {$id} not found");

        if( !$this->isGranted(CustomerVoter::CUSTOMER_DELETE, $customer) )
            throw $this->createAccessDeniedException('Access denied');

        $this->_manager->remove($customer);
        $this->_manager->flush();

        $this->_messages->markDeleteSuccess();

        return $this->redirectToRoute('customer_read');
    }
}