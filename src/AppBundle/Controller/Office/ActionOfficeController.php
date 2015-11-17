<?php
// AppBundle/Controller/Office/ActionOfficeController.php
namespace AppBundle\Controller\Office;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Security\Authorization\Voter\StudentVoter;

class ActionOfficeController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/customer_office/action/products/restrict_for/{id}",
     *      name="customer_office_action_products_restrict",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function restrictProductsAction(Request $request, $id)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException();

        if( !$this->isGranted(StudentVoter::STUDENT_BIND_PRODUCT, $student) )
            throw $this->createAccessDeniedException();

        if( !($productIds = $request->request->all()) )
            return new RedirectResponse($request->headers->get('referer'));

        foreach( $request->request->all() as $productId ) {
            $student->addProduct(
                $this->_manager->getReference('AppBundle:Product\Product', $productId)
            );
        }

        $this->_manager->persist($student);

        $this->_manager->flush();

        return $this->redirectToRoute('customer_office_students_products', [
            'id' => $id, 'permission' => 'allowed'
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/customer_office/action/products/allow_for/{id}",
     *      name="customer_office_action_products_allow",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"}
     * )
     */
    public function allowProductsAction(Request $request, $id)
    {
        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException();

        if( !$this->isGranted(StudentVoter::STUDENT_BIND_PRODUCT, $student) )
            throw $this->createAccessDeniedException();

        if( !($productIds = $request->request->all()) )
            return new RedirectResponse($request->headers->get('referer'));

        foreach( $request->request->all() as $productId ) {
            $student->removeProduct(
                $this->_manager->getReference('AppBundle:Product\Product', $productId)
            );
        }

        $this->_manager->persist($student);

        $this->_manager->flush();

        return $this->redirectToRoute('customer_office_students_products', [
            'id' => $id, 'permission' => 'restricted'
        ]);
    }

    /**
     * @Method({"POST"})
     * @Route(
     *      "/customer_office/action/daily_limit/update_for/{id}",
     *      name="customer_office_action_daily_limit_update",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+"},
     *      condition="request.isXmlHttpRequest()"
     * )
     */
    public function setStudentDailyLimit(Request $request, $id)
    {
        //TODO: Horrible, rewrite and put into service for fuck's sake

        $validateDailyLimit = function($dailyLimit)
        {
            $dailyLimit = str_replace(',', '.', str_replace('.', '', $dailyLimit));

            return ( is_numeric($dailyLimit) ) ? $dailyLimit : FALSE;
        };

        $isExceedsMin = function($dailyLimit, $minDailyLimit)
        {
            $result = bccomp($dailyLimit, $minDailyLimit, 2);

            return ( $result === 0 || $result === 1 ) ? TRUE : FALSE;
        };

        $student = $this->_manager->getRepository('AppBundle:Student\Student')->find($id);

        if( !$student )
            throw $this->createNotFoundException();

        $minDailyLimitSetting = $this->_manager->getRepository('AppBundle:Setting\Setting')->findStudentMinDailyLimit()->getSettingValue();

        if( !$minDailyLimitSetting )
            throw $this->createNotFoundException();

        if( !$this->isGranted(StudentVoter::STUDENT_UPDATE_DAILY_LIMIT, $student) )
            throw $this->createAccessDeniedException();

        if( $request->request->has('data') )
        {
            $data = $request->request->get('data');

            if( !$data[0] || !$data[0]['value'] )
                return new Response();

            if( !($dailyLimit = $validateDailyLimit($data[0]['value'])) )
                return new Response();

            if( !$isExceedsMin($dailyLimit, $minDailyLimitSetting) )
                $dailyLimit = $minDailyLimitSetting;

            $this->_manager->persist(
                $student->setDailyLimit($dailyLimit)
            );

            $this->_manager->flush();
        } else {
            return new Response();
        }

        return new Response(json_encode([
            'limit' => number_format($dailyLimit, 2, ',', '.')])
        );
    }
}