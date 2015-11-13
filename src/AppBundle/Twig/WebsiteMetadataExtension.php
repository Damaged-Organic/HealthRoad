<?php
// AppBundle/Twig/WebsiteMetadataExtension.php
namespace AppBundle\Twig;

use Exception;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManager;

use Twig_Extension,
    Twig_SimpleFunction;

class WebsiteMetadataExtension extends Twig_Extension
{
    private $_requestStack;
    private $_manager;

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->_requestStack = $requestStack;
    }

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function getFunctions()
    {
        return [
            'class' => new Twig_SimpleFunction('getMetadata', [$this, 'getMetadata']),
        ];
    }

    public function getMetadata()
    {
        $route = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        $metadata = $this->_manager->getRepository('AppBundle:Website\Metadata\Metadata')->findOneBy(['route' => $route]);

        if( !$metadata )
            throw new Exception("No metadata for route '{$route}' exists");

        return $metadata;
    }

    public function getName()
    {
        return 'website_metadata_extension';
    }
}