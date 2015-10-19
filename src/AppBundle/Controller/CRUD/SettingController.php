<?php
// src/AppBundle/Controller/CRUD/SettingController.php
namespace AppBundle\Controller\CRUD;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\SettingType,
    AppBundle\Security\Authorization\Voter\SettingVoter,
    AppBundle\Service\Security\SettingBoundlessAccess;

class SettingController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/setting/read",
     *      name="setting_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function readAction()
    {
        $_manager = $this->getDoctrine()->getManager();

        $_settingBoundlessAccess = $this->get('app.security.setting_boundless_access');

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        if( !$_settingBoundlessAccess->isGranted(SettingBoundlessAccess::SETTING_READ) )
            throw $this->createAccessDeniedException('Access denied');

        $setting = $_manager->getRepository('AppBundle:Setting\Setting')->findOne();

        $response = [
            'view' => 'AppBundle:Entity/Setting/CRUD:readList.html.twig',
            'data' => ['setting' => $setting]
        ];

        $_breadcrumbs->add('setting_read');

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
        $_manager = $this->getDoctrine()->getManager();

        $_breadcrumbs = $this->get('app.common.breadcrumbs');

        $setting = $_manager->getRepository('AppBundle:Setting\Setting')->findOne();

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
            $_manager->flush();

            return $this->redirectToRoute('setting_update');
        }

        $_breadcrumbs->add('setting_read')->add('setting_update');

        return $this->render('AppBundle:Entity/Setting/CRUD:updateList.html.twig', [
            'form' => $form->createView()
        ]);
    }
}