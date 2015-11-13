<?php
// AppBundle/Menu/WebsiteMenuBuilder.php
namespace AppBundle\Menu;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManager;

use Knp\Menu\FactoryInterface;

use AppBundle\Entity\Website\Menu\Utility\MenuBlockListInterface;

class WebsiteMenuBuilder implements MenuBlockListInterface
{
    private $_factory;
    private $_manager;
    private $_requestStack;

    public function setFactory(FactoryInterface $factory)
    {
        $this->_factory = $factory;
    }

    public function setManager(EntityManager $manager)
    {
        $this->_manager = $manager;
    }

    public function setRequestStack(RequestStack $requestStack)
    {
        $this->_requestStack = $requestStack;
    }

    public function createMainMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $items = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy(['block' => self::BLOCK_MAIN]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        foreach($items as $item)
        {
            $menu->addChild($item->getTitleShort(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleShort()]->setCurrent(TRUE);
        }

        return $menu;
    }

    public function createOurProjectMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy(['route' => 'website_our_project']);
        $minorItems = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy(['block' => self::BLOCK_OUR_PROJECT]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem->getTitleFull(), ['route' => $majorItem->getRoute()]);

            if( $majorItem->getRoute() === $currentRoute )
                $menu[$majorItem->getTitleFull()]->setCurrent(TRUE);
        }

        foreach($minorItems as $item)
        {
            $menu->addChild($item->getTitleFull(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleFull()]->setCurrent(TRUE);
        }

        return $menu;
    }

    public function createAboutCompanyMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy(['route' => 'website_about_company']);
        $minorItems = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy(['block' => self::BLOCK_ABOUT_COMPANY]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem->getTitleFull(), ['route' => $majorItem->getRoute()]);

            if( $majorItem->getRoute() === $currentRoute )
                $menu[$majorItem->getTitleFull()]->setCurrent(TRUE);
        }

        foreach($minorItems as $item)
        {
            $menu->addChild($item->getTitleFull(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleFull()]->setCurrent(TRUE);
        }

        return $menu;
    }

    public function createOurPartnersMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy(['route' => 'website_our_partners']);
        $minorItems = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->findAll();

        $menu->setExtra('currentElement', 'active');

        $currentRoute           = $this->_requestStack->getMasterRequest()->attributes->get('_route');
        $currentRouteParameters = [
            'id'   => $this->_requestStack->getMasterRequest()->get('id'),
            'slug' => $this->_requestStack->getMasterRequest()->get('slug')
        ];

        if( $majorItem )
        {
            $menu->addChild($majorItem->getTitleFull(), ['route' => $majorItem->getRoute()]);

            if( ($majorItem->getRoute() === $currentRoute) && (array_filter($currentRouteParameters) === []) )
                $menu[$majorItem->getTitleFull()]->setCurrent(TRUE);
            else
                $menu[$majorItem->getTitleFull()]->setCurrent(FALSE);
        }

        foreach($minorItems as $item)
        {
            $route           = 'website_our_partners';
            $routeParameters = [
                'id'   => $item->getId(),
                'slug' => $item->getSlug()
            ];

            $menu->addChild($item->getName(), [
                'route'           => $route,
                'routeParameters' => $routeParameters
            ]);

            if( ($route === $currentRoute) && ($routeParameters === $currentRouteParameters) )
            {
                $menu[$item->getName()]->setCurrent(TRUE);
            }
        }

        return $menu;
    }

    public function createProductsMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy(['route' => 'website_products']);
        $minorItems = $this->_manager->getRepository('AppBundle:Product\ProductCategory')->findAll();

        $menu->setExtra('currentElement', 'active');

        $currentRoute           = $this->_requestStack->getMasterRequest()->attributes->get('_route');
        $currentRouteParameters = [
            'product_category' => $this->_requestStack->getMasterRequest()->get('product_category')
        ];

        if( $majorItem )
        {
            $menu->addChild($majorItem->getTitleFull(), ['route' => $majorItem->getRoute()]);

            if( ($majorItem->getRoute() === $currentRoute) && (array_filter($currentRouteParameters) === []) )
                $menu[$majorItem->getTitleFull()]->setCurrent(TRUE);
            else
                $menu[$majorItem->getTitleFull()]->setCurrent(FALSE);
        }

        foreach($minorItems as $item)
        {
            $route           = 'website_products';
            $routeParameters = [
                'product_category' => $item->getId()
            ];

            $menu->addChild($item->getName(), [
                'route'           => $route,
                'routeParameters' => $routeParameters
            ]);

            if( ($route === $currentRoute) && ($routeParameters === $currentRouteParameters) )
            {
                $menu[$item->getName()]->setCurrent(TRUE);
            }
        }

        return $menu;
    }

    public function createContactsMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy(['route' => 'website_contacts']);
        $minorItems = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy(['block' => self::BLOCK_CONTACTS]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem->getTitleFull(), ['route' => $majorItem->getRoute()]);

            if( $majorItem->getRoute() === $currentRoute )
                $menu[$majorItem->getTitleFull()]->setCurrent(TRUE);
        }

        foreach($minorItems as $item)
        {
            $menu->addChild($item->getTitleFull(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleFull()]->setCurrent(TRUE);
        }

        return $menu;
    }

    public function createFooterMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $items = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findAll();

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        foreach($items as $item)
        {
            $menu->addChild($item->getTitleShort(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleShort()]->setCurrent(TRUE);
        }

        return $menu;
    }
}