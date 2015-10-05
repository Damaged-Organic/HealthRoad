<?php
// src/AppBundle/Controller/CRUD/SettingController.php
namespace AppBundle\Controller\CRUD;

use AppBundle\Form\Type\SettingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

class SettingController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route(
     *      "/settings",
     *      name="settings_read",
     *      host="{domain_dashboard}",
     *      defaults={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"},
     *      requirements={"_locale" = "%locale%", "domain_dashboard" = "%domain_dashboard%"}
     * )
     */
    public function readAction()
    {
        $_manager = $this->getDoctrine()->getManager();

        $setting = $_manager->getRepository('AppBundle:Setting\Setting')->find(1);

        $form = $this->createForm(new SettingType, $setting);

        return $this->render('AppBundle:Entity/Setting:form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}