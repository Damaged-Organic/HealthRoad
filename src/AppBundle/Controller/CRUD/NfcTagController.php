<?php
// AppBundle/Controller/CRUD/NfcTagController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Entity\NfcTag\NfcTag,
    AppBundle\Form\Type\NfcTagType,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Service\Security\NfcTagBoundlessAccess;

class NfcTagController extends Controller implements UserRoleListInterface
{
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
    public function readAction($id)
    {
        $_manager = $this->getDoctrine()->getManager();

        $_nfcTagBoundlessAccess = $this->get('app.security.nfc_tag_boundless_access');

        if( $id )
        {
            $nfcTag = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

            if( !$nfcTag )
                throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

            if( !$this->isGranted(NfcTagVoter::NFC_TAG_READ, $nfcTag) )
                throw $this->createAccessDeniedException('Access denied');

            $response = [
                'view' => 'AppBundle:Entity/NfcTag/CRUD:readItem.html.twig',
                'data' => ['nfcTag' => $nfcTag]
            ];
        } else {
            if( !$_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_READ) )
                throw $this->createAccessDeniedException('Access denied');

            $nfcTags = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAll();

            $response = [
                'view' => 'AppBundle:Entity/NfcTag/CRUD:readList.html.twig',
                'data' => ['nfcTags' => $nfcTags]
            ];
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
        $_nfcTagBoundlessAccess = $this->get('app.security.nfc_tag_boundless_access');

        if( !$_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE) )
            throw $this->createAccessDeniedException('Access denied');

        $nfcTagType = new NfcTagType($_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE));

        $form = $this->createForm($nfcTagType, $nfcTag = new NfcTag);

        $form->handleRequest($request);

        if( !($form->isValid()) ) {
            return $this->render('AppBundle:Entity/NfcTag/CRUD:createItem.html.twig', [
                'form' => $form->createView()
            ]);
        } else {
            $_manager = $this->getDoctrine()->getManager();

            $_manager->persist($nfcTag);
            $_manager->flush();

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
        $_manager = $this->getDoctrine()->getManager();

        $_nfcTagBoundlessAccess = $this->get('app.security.nfc_tag_boundless_access');

        $nfcTag = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_UPDATE, $nfcTag) ) {
            return $this->redirectToRoute('nfc_tag_read', [
                'id' => $nfcTag->getId()
            ]);
        }

        $nfcTagType = new NfcTagType($_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_CREATE));

        $form = $this->createForm($nfcTagType, $nfcTag);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $_manager->flush();

            if( $form->has('update_and_return') && $form->get('update_and_return')->isClicked() ) {
                return $this->redirectToRoute('nfc_tag_read');
            } else {
                return $this->redirectToRoute('nfc_tag_update', [
                    'id' => $nfcTag->getId()
                ]);
            }
        }

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
        $_manager = $this->getDoctrine()->getManager();

        $nfcTag = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("NfcTag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_DELETE, $nfcTag) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager->remove($nfcTag);
        $_manager->flush();

        return $this->redirectToRoute('nfc_tag_read');
    }
}