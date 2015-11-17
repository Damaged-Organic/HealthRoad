<?php
// AppBundle/Controller/Website/IeFallbackController.php
namespace AppBundle\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class IeFallbackController
{
    private $_templating;

    public function setTemplating(EngineInterface $templating)
    {
        $this->_templating = $templating;
    }

    public function ieFallbackAction()
    {
        return $this->_templating->renderResponse('AppBundle:Website/Fallback:ie.html.twig');
    }
}