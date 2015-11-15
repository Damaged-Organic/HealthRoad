<?php
// AppBundle/Controller/Office/OfficeController.php
namespace AppBundle\Controller\Office;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

class OfficeController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("app.common.breadcrumbs") */
    private $_breadcrumbs;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office",
     *      name="customer_office",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function personalAction()
    {
        $menu = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy([
            'route' => ['customer_office']
        ]);

        if( !$menu )
            throw $this->createNotFoundException();

        $this->_breadcrumbs->add('customer_office', [], $menu->getTitleShort());

        $customer = $this->_manager->getRepository('AppBundle:Customer\Customer')->find($this->getUser()->getId());

        if( !$customer )
            throw $this->createNotFoundException();

        return $this->render('AppBundle:Office/State:personal.html.twig', [
            'customer' => $customer
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office/students/{id}",
     *      name="customer_office_students",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%", "id" = null},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function studentsAction($id = NULL)
    {
        $menu = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy([
            'route' => ['customer_office_students']
        ]);

        if( !$menu )
            throw $this->createNotFoundException();

        if( $id )
        {
            $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

            if( !$student )
                throw $this->createNotFoundException();

            $this->_breadcrumbs
                ->add('customer_office_students', [], $menu->getTitleShort())
                ->add('customer_office_students', ['id' => $student->getId()], $student->getName())
            ;

            $response = [
                'view' => 'AppBundle:Office/State:student.html.twig',
                'data' => ['student' => $student]
            ];
        } else {
            $students = $this->_manager->getRepository('AppBundle:Student\Student')->findBy(['customer' => $this->getUser()->getId()]);

            $this->_breadcrumbs
                ->add('customer_office', [], $menu->getTitleShort())
            ;

            $response = [
                'view' => 'AppBundle:Office/State:students.html.twig',
                'data' => ['students' => $students]
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office/students/{id}/purchases",
     *      name="customer_office_students_purchases",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function studentsPurchasesAction($id)
    {
        $menu = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy([
            'route' => ['customer_office_students', 'customer_office_students_purchases']
        ]);

        if( !$menu )
            throw $this->createNotFoundException();

        $criteria_1 = Criteria::create()->where(Criteria::expr()->in("route", ['customer_office_students']));
        $criteria_2 = Criteria::create()->where(Criteria::expr()->in("route", ['customer_office_students_purchases']));

        $menu_1 = (new ArrayCollection($menu))->matching($criteria_1)->first()->getTitleShort();
        $menu_2 = (new ArrayCollection($menu))->matching($criteria_2)->first()->getTitleShort();

        // ---

        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException();

        $purchases = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findBy(
            ['nfcTag' => $student->getNfcTag()->getId()],
            ['syncPurchasedAt' => 'DESC']
        );

        $this->_breadcrumbs
            ->add('customer_office_students', [], $menu_1)
            ->add('customer_office_students', ['id' => $student->getId()], $student->getName())
            ->add('customer_office_students_purchases', ['id' => $student->getId()], $menu_2)
        ;

        return $this->render('AppBundle:Office/State:studentPurchases.html.twig', [
            'student'   => $student,
            'purchases' => $purchases
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office/students/{id}/products",
     *      name="customer_office_students_products",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function studentsProductsAction($id)
    {
        $menu = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findBy([
            'route' => ['customer_office_students', 'customer_office_students_products']
        ]);

        if( !$menu )
            throw $this->createNotFoundException();

        $criteria_1 = Criteria::create()->where(Criteria::expr()->in("route", ['customer_office_students']));
        $criteria_2 = Criteria::create()->where(Criteria::expr()->in("route", ['customer_office_students_products']));

        $menu_1 = (new ArrayCollection($menu))->matching($criteria_1)->first()->getTitleShort();
        $menu_2 = (new ArrayCollection($menu))->matching($criteria_2)->first()->getTitleShort();

        // ---

        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException();

        $this->_breadcrumbs
            ->add('customer_office_students', [], $menu_1)
            ->add('customer_office_students', ['id' => $student->getId()], $student->getName())
            ->add('customer_office_students_products', ['id' => $student->getId()], $menu_2)
        ;

        return $this->render('AppBundle:Office/State:studentProducts.html.twig', [
            'student' => $student
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/customer_office/purchases/{id}",
     *      name="customer_office_purchases",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%", "id" = null},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function purchasesAction($id = NULL)
    {
        $extractNfcTags = function(array $students)
        {
            $nfcTags = [];

            foreach($students as $student)
            {
                if( $student->getNfcTag() )
                    $nfcTags[] = $student->getNfcTag()->getId();
            }

            return $nfcTags;
        };

        $menu = $this->_manager->getRepository('AppBundle:Website\Menu\Menu')->findOneBy([
            'route' => ['customer_office_purchases']
        ]);

        if( !$menu )
            throw $this->createNotFoundException();

        $this->_breadcrumbs->add('customer_office', [], $menu->getTitleShort());

        $students = $this->_manager->getRepository('AppBundle:Student\Student')->findBy(['customer' => $this->getUser()->getId()]);

        if( $id )
        {
            $criteria = Criteria::create()->where(Criteria::expr()->in("id", [$id]));

            $nfcTagCondition = (new ArrayCollection($students))->matching($criteria)->first()->getNfcTag()->getId();

        } else {
            $nfcTagCondition = $extractNfcTags($students);
        }

        $purchases = $this->_manager->getRepository('AppBundle:Purchase\Purchase')->findBy(
            ['nfcTag' => $nfcTagCondition],
            ['syncPurchasedAt' => 'DESC']
        );

        return $this->render('AppBundle:Office/State:purchases.html.twig', [
            'students'  => $students,
            'purchases' => $purchases
        ]);
    }
}