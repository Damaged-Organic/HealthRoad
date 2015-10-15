<?php
// AppBundle/Controller/CRUD/SettlementController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\Settlement\Settlement,
    AppBundle\Form\Type\SettlementType,
    AppBundle\Security\Authorization\Voter\SettlementVoter,
    AppBundle\Service\Security\SettlementBoundlessAccess;

class SettlementController extends Controller implements UserRoleListInterface
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/{id}",
     *      name="settlement_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_settlementBoundlessAccess = $this->get('app.security.settlement_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( $id )
        {
            $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

            if( !$settlement )
                throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

            if( !$this->isGranted(SettlementVoter::SETTLEMENT_READ, $settlement) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/Settlement/CRUD:readItem.html.twig',
                'data' => ['settlement' => $settlement]
            ];
        } else {
            if( !$_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $settlements = $_manager->getRepository('AppBundle:Settlement\Settlement')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/Settlement/CRUD:readList.html.twig',
                'data' => ['settlements' => $settlements]
            ];
        }

        $_breadcrumbs->add('settlement_read');

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/settlement/create",
     *      name="settlement_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        $_settlementBoundlessAccess = $this->get('app.security.settlement_boundless_access');

        if( !$_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $settlementType = new SettlementType($_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE));

        $form = $this->createForm($settlementType, $settlement = new Settlement, [
            'action' => $this->generateUrl('settlement_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $_breadcrumbs->add('settlement_read')->add('settlement_create');

            return $this->render('AppBundle:Entity/Settlement/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($settlement);
            $_manager->flush();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('settlement_read');
            } else {
                return $this->redirectToRoute('settlement_update', [
                    'id' => $settlement->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/settlement/update/{id}",
     *      name="settlement_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_settlementBoundlessAccess = $this->get('app.security.settlement_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_UPDATE, $settlement) ) {
            return $this->redirectToRoute('settlement_read', [
                'id' => $settlement->getId()
            ]);
        }

        $settlementType = new SettlementType($_settlementBoundlessAccess->isGranted(SettlementBoundlessAccess::SETTLEMENT_CREATE));

        $form = $this->createForm($settlementType, $settlement, [
            'action' => $this->generateUrl('settlement_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('settlement_read');
            } else {
                return $this->redirectToRoute('settlement_update', [
                    'id' => $settlement->getId()
                ]);
            }
        }

        $_breadcrumbs->add('settlement_read')->add('settlement_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/Settlement/CRUD:updateItem.html.twig', [
            'form'       => $form->createView(),
            'settlement' => $settlement
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/settlement/delete/{id}",
     *      name="settlement_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $settlement = $_manager->getRepository('AppBundle:Settlement\Settlement')->find($id);

        if( !$settlement )
            throw $this->createNotFoundException("Settlement identified by `id` {$id} not found");

        if( !$this->isGranted(SettlementVoter::SETTLEMENT_DELETE, $settlement) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($settlement);
        $_manager->flush();

        return $this->redirectToRoute('settlement_read');
    }
}