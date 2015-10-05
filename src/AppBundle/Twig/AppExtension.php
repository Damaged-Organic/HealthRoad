<?php
// AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use Twig_Extension,
    Twig_SimpleFunction;

use ReflectionClass;

class AppExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            'class' => new Twig_SimpleFunction('class', [$this, 'getClass']),
        ];
    }

    public function getClass($object)
    {
        return (new ReflectionClass($object))->getShortName();
    }

    public function getName()
    {
        return 'app_extension';
    }
}