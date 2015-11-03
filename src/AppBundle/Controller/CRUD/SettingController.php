<?php
// src/AppBundle/Controller/CRUD/SettingController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Form\Type\SettingType,
    AppBundle\Security\Authorization\Voter\SettingVoter,
    AppBundle\Service\Security\SettingBoundlessAccess;

class SettingController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.security.setting_boundless_access") */
    private $_settingBoundlessAccess;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/setting",
     *      name="setting_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function readAction()
    {
        if( !$this->_settingBoundlessAccess->isGranted(SettingBoundlessAccess::SETTING_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $setting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findOne();

        $response = [
            'view' => 'AppBundle:Entity/Setting/CRUD:readList.html.twig',
            'data' => ['setting' => $setting]
        ];

        $this->_breadcrumbs->add('setting_read');

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/setting/update",
     *      name="setting_update",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function updateAction(Request $request)
    {
        $setting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findOne();

        if( !$setting )
            throw $this->createNotFoundException("Parent Setting object not found");

        if( !$this->isGranted(SettingVoter::SETTING_UPDATE, $setting) )
            return $this->redirectToRoute('setting_read');

        $form = $this->createForm(new SettingType, $setting, [
            'action' => $this->generateUrl('setting_update')
        ]);

        $form->handleRequest($request);

        if( $form->isValid() )
        {
            $this->_manager->flush();

            $this->_messages->markUpdateSuccess();

            return $this->redirectToRoute('setting_update');
        }

        $this->_breadcrumbs->add('setting_read')->add('setting_update');

        return $this->render('AppBundle:Entity/Setting/CRUD:updateList.html.twig', [
            'form'    => $form->createView(),
            'setting' => $setting
        ]);
    }
}