<?php
// AppBundle/Menu/WebsiteMenuBuilder.php
namespace AppBundle\Menu;

use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManager;

use Knp\Menu\FactoryInterface;

use AppBundle\Entity\WebsiteMenu\Utility\MenuBlockListInterface;

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

        $items = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['block' => self::BLOCK_MAIN]);

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

        $majorItem  = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['route' => 'website_our_project'], NULL, 1);
        $minorItems = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['block' => self::BLOCK_OUR_PROJECT]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem[0]->getTitleFull(), ['route' => $majorItem[0]->getRoute()]);

            if( $majorItem[0]->getRoute() === $currentRoute )
                $menu[$majorItem[0]->getTitleFull()]->setCurrent(TRUE);
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

        $majorItem = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['route' => 'website_about_company'], NULL, 1);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem[0]->getTitleFull(), ['route' => $majorItem[0]->getRoute()]);

            if( $majorItem[0]->getRoute() === $currentRoute )
                $menu[$majorItem[0]->getTitleFull()]->setCurrent(TRUE);
        }

        return $menu;
    }

    public function createContactsMenu(array $options)
    {
        $menu = $this->_factory->createItem('root');

        $majorItem  = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['route' => 'website_contacts'], NULL, 1);
        $minorItems = $this->_manager->getRepository('AppBundle:WebsiteMenu\WebsiteMenu')->findBy(['block' => self::BLOCK_CONTACTS]);

        $menu->setExtra('currentElement', 'active');

        $currentRoute = $this->_requestStack->getMasterRequest()->attributes->get('_route');

        if( $majorItem )
        {
            $menu->addChild($majorItem[0]->getTitleFull(), ['route' => $majorItem[0]->getRoute()]);

            if( $majorItem[0]->getRoute() === $currentRoute )
                $menu[$majorItem[0]->getTitleFull()]->setCurrent(TRUE);
        }

        foreach($minorItems as $item)
        {
            $menu->addChild($item->getTitleFull(), ['route' => $item->getRoute()]);

            if( $item->getRoute() === $currentRoute )
                $menu[$item->getTitleFull()]->setCurrent(TRUE);
        }

        return $menu;
    }
}