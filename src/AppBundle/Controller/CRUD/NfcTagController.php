<?php
// AppBundle/Controller/CRUD/NfcTagController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Form\Type\NfcTagType,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Service\Security\NfcTagBoundlessAccess;

class NfcTagController extends Controller implements UserRoleListInterface
{
    use EntityFilter;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.security.nfc_tag_boundless_access") */
    private $_nfcTagBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/{id}",
     *      name="nfc_tag_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = null},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function readAction($id = NULL)
    {
        if( $id )
        {
            $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

            if( !$nfcTag )
                throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

            if( !$this->isGranted(NfcTagVoter::NFC_TAG_READ, $nfcTag) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/NfcTag/CRUD:readItem.html.twig',
                'data' => ['nfcTag' => $nfcTag]
            ];

            $this->_breadcrumbs->add('nfc_tag_read')->add('nfc_tag_read', ['id' => $id], $this->_translator->trans('nfc_tag_view', [], 'routes'));
        } else {
            if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $nfcTags = $this->filterDeletedIfNotGranted(
                NfcTagVoter::NFC_TAG_READ,
                $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAll()
            );

            $response = [
                'view' => 'AppBundle:Entity/NfcTag/CRUD:readList.html.twig',
                'data' => ['nfcTags' => $nfcTags]
            ];

            $this->_breadcrumbs->add('nfc_tag_read');
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/nfc_tag/create",
     *      name="nfc_tag_create",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function createAction(Request $request)
    {
        if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $nfcTagType = new NfcTagType($this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE));

        $form = $this->createForm($nfcTagType, $nfcTag = new NfcTag, [
            'action' => $this->generateUrl('nfc_tag_create')
        ]);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            $this->_breadcrumbs->add('nfc_tag_read')->add('nfc_tag_create');

            return $this->render('AppBundle:Entity/NfcTag/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $this->_manager->persist($nfcTag);
            $this->_manager->flush();

            $this->_messages->markCreateSuccess();

            if( $form->has('create_and_return') && $form->get('create_and_return')->isClicked() ) {
                return $this->redirectToRoute('nfc_tag_read');
            } else {
                return $this->redirectToRoute('nfc_tag_update', [
                    'id' => $nfcTag->getId()
                ]);
            }
        }
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/nfc_tag/update/{id}",
     *      name="nfc_tag_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function updateAction(Request $request, $id)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_UPDATE, $nfcTag) ) {
            return $this->redirectToRoute('nfc_tag_read', [
                'id' => $nfcTag->getId()
            ]);
        }

        $nfcTagType = new NfcTagType($this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE));

        $form = $this->createForm($nfcTagType, $nfcTag, [
            'action' => $this->generateUrl('nfc_tag_update', ['id' => $id])
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('nfc_tag_read');
            } else {
                return $this->redirectToRoute('nfc_tag_update', [
                    'id' => $nfcTag->getId()
                ]);
            }
        }

        $this->_breadcrumbs->add('nfc_tag_read')->add('nfc_tag_update', ['id' => $id]);

        return $this->render('AppBundle:Entity/NfcTag/CRUD:updateItem.html.twig', [
            'form'   => $form->createView(),
            'nfcTag' => $nfcTag
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/delete/{id}",
     *      name="nfc_tag_delete",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+"}
     * )
     */
    public function deleteAction($id)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_DELETE, $nfcTag) )
            throw $this->createAccessDeniedException('Access denied');

        if( !$nfcTag->getPseudoDeleted() )
        {
            $nfcTag->setPseudoDeleted(TRUE);

            $this->_manager->flush();

            $this->_messages->markDeleteSuccess();
        } else {
            $nfcTag->setPseudoDeleted(FALSE);

            $this->_manager->flush();

            $this->_messages->markUnDeleteSuccess();
        }

        return $this->redirectToRoute('nfc_tag_read');
    }
}