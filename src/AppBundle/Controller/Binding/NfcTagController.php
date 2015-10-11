<?php
// AppBundle/Controller/Binding/NfcTagController.php
namespace AppBundle\Controller\Binding;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use AppBundle\Controller\Utility\Traits\ClassOperationsTrait,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface,
    AppBundle\Service\Security\NfcTagBoundlessAccess,
    AppBundle\Security\Authorization\Voter\NfcTagVoter,
    AppBundle\Entity\VendingMachine\VendingMachine,
    AppBundle\Entity\Student\Student;

class NfcTagController extends Controller implements UserRoleListInterface
{
    use ClassOperationsTrait;

    public function showAction($objectClass, $objectId)
    {
        $_nfcTagBoundlessAccess = $this->get('app.security.nfc_tag_boundless_access');

        if( !$_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                /*
                 * TRICKY: single nfcTag object pushed into array in order to be valid for template
                 */
                $nfcTags = [$student->getNfcTag()];
            break;

            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$vendingMachine )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $nfcTags = $vendingMachine->getNfcTags();
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        return $this->render('AppBundle:Entity/NfcTag/Binding:show.html.twig', [
            'nfcTags'     => $nfcTags,
            'objectId'    => $objectId,
            'objectClass' => $objectClass
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
        $_nfcTagBoundlessAccess = $this->get('app.security.nfc_tag_boundless_access');

        if( !$_nfcTagBoundlessAccess->isGranted(NfcTagBoundlessAccess::NFC_TAG_BIND) )
            throw $this->createAccessDeniedException('Access denied');

        $_manager = $this->getDoctrine()->getManager();

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($student),
                    'id'    => $student->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$vendingMachine )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $object = [
                    'class' => $this->getObjectClassName($vendingMachine),
                    'id'    => $vendingMachine->getId()
                ];
            break;

            default:
                throw new NotAcceptableHttpException("Object not supported");
            break;
        }

        $nfcTags = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->findAll();

        return $this->render('AppBundle:Entity/NfcTag/Binding:choose.html.twig', [
            'nfcTags'     => $nfcTags,
            'objectClass' => $object['class'],
            'objectId'    => $object['id']
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/nfc_tag/bind",
     *      name="nfc_tag_bind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function bindToAction(Request $request)
    {
        $nfcTagId = ( $request->request->has('nfcTagId') ) ? $request->request->get('nfcTagId') : NULL;

        $_manager = $this->getDoctrine()->getManager();

        $nfcTag = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($nfcTagId);

        if( !$nfcTag )
            throw $this->createNotFoundException("Nfc Tag identified by `id` {$nfcTagId} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException('Access denied');

        $objectClass = ( $request->request->get('objectClass') ) ? $request->request->get('objectClass') : NULL;
        $objectId    = ( $request->request->get('objectId') ) ? $request->request->get('objectId') : NULL;

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                $student = $_manager->getRepository('AppBundle:Student\Student')->find($objectId);

                if( !$student )
                    throw $this->createNotFoundException("Student identified by `id` {$objectId} not found");

                /*
                 * TRICKY: had to set NfcTag as the owning side of the oneToOne relationship
                 * due to possible change to manyToOne. If so, a `student_id` column will remain unchanged.
                 * But in that case simply persisting nfcTag is no enough, as it could generate an exception
                 * because of the integrity violation; so student's nfcTag relationship should first be
                 * persisted as `NULL` and flushed in order to break that relationship.
                 */
                $_manager->transactional(function($_manager) use($student, $nfcTag)
                {
                    if( $student->getNfcTag() )
                    {
                        $_manager->persist(
                            $student->getNfcTag()->setStudent(NULL)
                        );
                        $_manager->flush();
                    }

                    $nfcTag->setStudent($student);

                    $_manager->persist($nfcTag);
                });

                $redirect = [
                    'route' => "student_update",
                    'id'    => $student->getId()
                ];
            break;

            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                $vendingMachine = $_manager->getRepository('AppBundle:VendingMachine\VendingMachine')->find($objectId);

                if( !$vendingMachine )
                    throw $this->createNotFoundException("Vending Machine identified by `id` {$objectId} not found");

                $vendingMachine->addNfcTag($nfcTag);

                $_manager->persist($vendingMachine);

                $redirect = [
                    'route' => "vending_machine_update",
                    'id'    => $vendingMachine->getId()
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
     *      "/nfc_tag/unbind/{id}/{objectClass}/{objectId}",
     *      name="nfc_tag_unbind",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%", "id" = "\d+", "objectClass" = "[a-z]+", "objectId" = "\d+"}
     * )
     */
    public function unbindFromAction($id, $objectClass, $objectId)
    {
        $_manager = $this->getDoctrine()->getManager();

        $nfcTag = $_manager->getRepository('AppBundle:NfcTag\NfcTag')->find($id);

        if( !$nfcTag )
            throw $this->createNotFoundException("Nfc Tag identified by `id` {$id} not found");

        if( !$this->isGranted(NfcTagVoter::NFC_TAG_BIND, $nfcTag) )
            throw $this->createAccessDeniedException('Access denied');

        switch(TRUE)
        {
            case $this->compareObjectClassNameToString(new Student, $objectClass):
                //this should be gone in AJAX version
                $studentId = $nfcTag->getStudent()->getId();

                $nfcTag->setStudent(NULL);

                $redirect = [
                    'route' => "student_update",
                    'id'    => $studentId
                ];
            break;

            case $this->compareObjectClassNameToString(new VendingMachine, $objectClass):
                //this should be gone in AJAX version
                $vendingMachineId = $nfcTag->getVendingMachine()->getId();

                $nfcTag->setVendingMachine(NULL);

                $redirect = [
                    'route' => "vending_machine_update",
                    'id'    => $vendingMachineId
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