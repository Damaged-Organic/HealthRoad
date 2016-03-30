<?php
// AppBundle/Controller/Binding/NfcTagController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Service\Security\NfcTagBoundlessAccess,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\Student\Student,
    AppBundle\Security\Authorization\Voter\StudentVoter,
    AppBundle\Security\Authorization\Voter\VendingMachineVoter;

class NfcTagController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait, EntityFilter;

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

    public function showAction($objectClass, $objectId)
    {
        if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_READ) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                /*
                 * TRICKY: single nfcTag object pushed into array in order to be valid for template
                 */
                $nfcTags = $this->filterDeletedIfNotGranted(
                    NfcTagVoter::NFC_TAG_READ,
                    (( $object->getNfcTag() ) ? [$object->getNfcTag()] : NULL)
                );

                $action = [
                    'path'  => 'nfc_tag_choose',
                    'voter' => StudentVoter::STUDENT_BIND
                ];
            break;

            /*
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $object = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$object )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $nfcTags = $object->getNfcTags();

                $action = [
                    'path'  => 'nfc_tag_choose',
                    'voter' => VendingMachineVoter::VENDING_MACHINE_BIND
                ];
            break;
            */

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/NfcTag/Binding:show.html.twig', [
            'standalone' => TRUE,
            'nfcTags'    => $nfcTags,
            'object'     => $object,
            'action'     => $action
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/choose_for/{objectClass}/{objectId}",
     *      name="nfc_tag_choose",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function chooseAction($objectClass, $objectId)
    {
        if( !$this->_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $object = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $path = 'student_update_bounded';

                $this->_breadcrumbs->add('student_read')->add('student_update', ['id' => $objectId])->add('student_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'nfctag'
                    ],
                    $this->_translator->trans('nfc_tag_read', [], 'routes')
                );
            break;

            /*
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $vendingMachine = $object = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$vendingMachine )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $path = 'vending_machine_update_bounded';

                $_breadcrumbs->add('vending_machine_read')->add('vending_machine_update', ['id' => $objectId])->add('vending_machine_update_bounded',
                    [
                        'objectId'    => $objectId,
                        'objectClass' => 'nfctag'
                    ],
                    $_translator->trans('nfc_tag_read', [], 'routes')
                );
            break;
            */

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $nfcTags = $this->filterDeletedIfNotGranted(
            NfcTagVoter::NFC_TAG_READ,
            $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAll()
        );

        $this->_breadcrumbs->add('nfc_tag_choose', [
            'objectId'    => $objectId,
            'objectClass' => $objectClass
        ]);

        return $this->render('AppBundle:Entity/NfcTag/Binding:choose.html.twig', [
            'path'    => $path,
            'nfcTags' => $nfcTags,
            'object'  => $object
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/bind/{targetId}/{objectClass}/{objectId}",
     *      name="nfc_tag_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function bindToAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($targetId);

        if( !$nfcTag )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

                /*
                 * TRICKY: had to set NfcTag as the owning side of the oneToOne relationship
                 * due to possible change to manyToOne. If so, a `student_id` column will remain unchanged.
                 * But in that case simply persisting nfcTag is no enough, as it could generate an exception
                 * because of the integrity violation; so student's nfcTag relationship should first be
                 * persisted as `NULL` and flushed in order to break that relationship.
                 */
                $this->_manager->transactional(function($_manager) use($student, $nfcTag)
                {
                    if( $student->getNfcTag() )
                    {
                        $_manager->persist(
                            $student->getNfcTag()->setStudent(NULL)
                        );
                        $_manager->flush();
                    }

                    $nfcTag->setStudent($student);

                    $nfcTag->deactivate();

                    $_manager->persist($nfcTag);
                });
            break;

            /*
            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$vendingMachine )
                    throw $this->createNotFoundException($_translator->trans('common.error.not_found', [], 'responses'));

                $vendingMachine->addNfcTag($nfcTag);

                $_manager->persist($vendingMachine);
            break;
            */

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_boundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markBindSuccess(
            $this->_translator->trans('bind.success.nfc_tag', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/nfc_tag/unbind/{targetId}/{objectClass}/{objectId}",
     *      name="nfc_tag_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "targetId" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction(Request $request, $targetId, $objectClass, $objectId)
    {
        $nfcTag = $this->_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($targetId);

        if( !$nfcTag )
            throw $this->createNotFoundException($this->_translator->trans('common.error.not_found', [], 'responses'));

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException($this->_translator->trans('common.error.forbidden', [], 'responses'));

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $nfcTag->deactivate();

                $nfcTag->setStudent(NULL);
            break;

            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $nfcTag->setVendingMachine(NULL);
            break;

            default:
                throw new NotAcceptableHttpException($this->_translator->trans('bind.error.not_unboundalbe', [], 'responses'));
            break;
        }

        $this->_manager->flush();

        $this->_messages->markUnbindSuccess(
            $this->_translator->trans('unbind.success.nfc_tag', [], 'responses')
        );

        return new RedirectResponse($request->headers->get('referer'));
    }
}
