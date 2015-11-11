<?php
// AppBundle/Controller/Website/CommonWebsiteController.php
namespace AppBundle\Controller\Website;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommonWebsiteController extends Controller
{
    public function headerAction()
    {
        return $this->render('AppBundle:Website/Common:header.html.twig');
    }

    public function footerAction()
    {
        return $this->render('AppBundle:Website/Common:footer.html.twig');
    }
}