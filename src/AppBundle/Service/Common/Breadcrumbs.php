<?php
// AppBundle/Service/Common/Breadcrumbs.php
namespace AppBundle\Service\Common;

use Symfony\Component\Routing\Router,
    Symfony\Component\Translation\TranslatorInterface;

class Breadcrumbs
{
    private $_router;
    private $_translator;

    private $breadcrumbs;

    public function setRouter(Router $router)
    {
        $this->_router = $router;
    }

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->_translator = $translator;
    }

    public function add($route, array $arguments = [], $title = NULL)
    {
        $url = $this->_router->generate($route, $arguments);

        $title = ( $title ) ?: $this->_translator->trans($route, [], 'routes');

        $this->breadcrumbs[] = [
            'url'   => $url,
            'title' => $title
        ];

        return $this;
    }

    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }
}