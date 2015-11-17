<?php
// AppBundle/Controller/Website/WebsiteController.php
namespace AppBundle\Controller\Website;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use JMS\DiExtraBundle\Annotation as DI;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Controller\Utility\Traits\EntityFilter,
    AppBundle\Controller\Utility\Traits\FormErrorsTrait,
    AppBundle\Entity\Website\Feedback\Feedback,
    AppBundle\Entity\Website\Feedback\FeedbackOrder,
    AppBundle\Entity\Website\Feedback\FeedbackSupplier,
    AppBundle\Form\Type\Website\FeedbackType,
    AppBundle\Form\Type\Website\FeedbackOrderType,
    AppBundle\Form\Type\Website\FeedbackSupplierType;

class WebsiteController extends Controller
{
    use EntityFilter, FormErrorsTrait;

    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /** @DI\Inject("translator") */
    private $_translator;

    /** @DI\Inject("app.common.messages") */
    private $_messages;

    /** @DI\Inject("app.website.feedback_mailer") */
    private $_feedbackMailer;

    /**
     * @Method({"GET"})
     * @Route(
     *      "/",
     *      name="website_index",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function indexAction()
    {
        $suppliers = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->findAll();

        return $this->render('AppBundle:Website/State:index.html.twig', [
            'suppliers' => $suppliers
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project",
     *      name="website_our_project",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function ourProjectAction()
    {
        return $this->render('AppBundle:Website/State:ourProject.html.twig');
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/our_project/how_to_get_card",
     *      name="website_how_to_get_card",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToGetCardAction(Request $request)
    {
        $form = $this->createForm(new FeedbackOrderType($this->_translator), $feedbackOrder = new FeedbackOrder);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if ($form->isValid()) {
                $this->_manager->flush();

                if ($this->_feedbackMailer->sendFeedbackOrder($feedbackOrder)) {
                    $this->_messages->markWebsiteFeedbackSuccess();
                } else {
                    $this->_messages->markWebsiteFeedbackError();
                }

                return $this->redirectToRoute('website_how_to_get_card');
            } else {
                $this->_messages->markWebsiteFeedbackErrors(
                    $this->getFormErrorMessages($form)
                );
            }
        }

        return $this->render('AppBundle:Website/State:howToGetCard.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project/how_to_replenish_card",
     *      name="website_how_to_replenish_card",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToReplenishCardAction()
    {
        return $this->render('AppBundle:Website/State:howToReplenishCard.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_project/how_to_use_vending_machine",
     *      name="website_how_to_use_vending_machine",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function howToUseVendingMachineAction()
    {
        return $this->render('AppBundle:Website/State:howToUseVendingMachine.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/about_company",
     *      name="website_about_company",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function aboutCompanyAction()
    {
        return $this->render('AppBundle:Website/State:aboutCompany.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/news",
     *      name="website_news",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function newsAction()
    {
        return $this->render('AppBundle:Website/State:aboutCompany.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/promotions",
     *      name="website_promotions",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function promotionsAction()
    {
        return $this->render('AppBundle:Website/State:aboutCompany.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/gallery",
     *      name="website_gallery",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function galleryAction()
    {
        return $this->render('AppBundle:Website/State:aboutCompany.html.twig');
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/our_partners/{id}/{slug}",
     *      name="website_our_partners",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%", "id" = null, "slug" = null},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+", "slug" = "[0-9a-z_-]+"}
     * )
     */
    public function ourPartnersAction($id = NULL)
    {
        if( $id )
        {
            $supplier = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->find($id);

            if( !$supplier )
                throw $this->createNotFoundException();

            $response = [
                'view' => 'AppBundle:Website/State:ourPartner.html.twig',
                'data' => ['supplier' => $supplier]
            ];
        } else {
            $suppliers = $this->_manager->getRepository('AppBundle:Supplier\Supplier')->findAll();

            $response = [
                'view' => 'AppBundle:Website/State:ourPartners.html.twig',
                'data' => ['suppliers' => $suppliers]
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/products/{id}/{slug}",
     *      name="website_products",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%", "id" = null, "slug" = null},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%", "id" = "\d+", "slug" = "[0-9a-z_-]+"}
     * )
     */
    public function productsAction(Request $request, $id = NULL)
    {
        if( $id )
        {
            $product = $this->_manager->getRepository('AppBundle:Product\Product')->findOneBy([
                'id'            => $id,
                'pseudoDeleted' => FALSE
            ]);

            if( !$product )
                throw $this->createNotFoundException();

            $response = [
                'view' => 'AppBundle:Website/State:product.html.twig',
                'data' => ['product' => $product]
            ];
        } else {
            if( $request->query->has('product_category') ) {
                $products = $this->filterDeleted(
                    $this->_manager->getRepository('AppBundle:Product\Product')->findBy([
                        'productCategory' => $request->query->get('product_category')
                    ])
                );
            } else {
                $products = $this->filterDeleted(
                    $this->_manager->getRepository('AppBundle:Product\Product')->findAll()
                );
            }

            $response = [
                'view' => 'AppBundle:Website/State:products.html.twig',
                'data' => ['products' => $products]
            ];
        }

        return $this->render($response['view'], $response['data']);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/contacts",
     *      name="website_contacts",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function contactsAction()
    {
        $contact = $this->_manager->getRepository('AppBundle:Website\Contact\Contact')->findOneBy([
            'alias' => 'support_center'
        ]);

        if( !$contact )
            throw $this->createNotFoundException();

        return $this->render('AppBundle:Website/State:contacts.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * @Method({"GET"})
     * @Route(
     *      "/contacts/vending_machines_placement",
     *      name="website_vending_machines_placement",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function contactsPlacementAction()
    {
        $contact = $this->_manager->getRepository('AppBundle:Website\Contact\Contact')->findOneBy([
            'alias' => 'contact_placement'
        ]);

        if( !$contact )
            throw $this->createNotFoundException();

        return $this->render('AppBundle:Website/State:contactsPlacement.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/contacts/vending_machines_suppliers",
     *      name="website_vending_machines_suppliers",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function contactsSuppliersAction(Request $request)
    {
        $contact = $this->_manager->getRepository('AppBundle:Website\Contact\Contact')->findOneBy([
            'alias' => 'contact_suppliers'
        ]);

        if( !$contact )
            throw $this->createNotFoundException();

        $form = $this->createForm(new FeedbackSupplierType($this->_translator), $feedbackSupplier = new FeedbackSupplier);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if ($form->isValid()) {
                $this->_manager->flush();

                if ($this->_feedbackMailer->sendFeedbackSupplier($feedbackSupplier)) {
                    $this->_messages->markWebsiteFeedbackSuccess();
                } else {
                    $this->_messages->markWebsiteFeedbackError();
                }

                return $this->redirectToRoute('website_vending_machines_suppliers');
            } else {
                $this->_messages->markWebsiteFeedbackErrors(
                    $this->getFormErrorMessages($form)
                );
            }
        }

        return $this->render('AppBundle:Website/State:contactsSuppliers.html.twig', [
            'contact' => $contact,
            'form'    => $form->createView()
        ]);
    }

    /**
     * @Method({"GET", "POST"})
     * @Route(
     *      "/feedback",
     *      name="website_feedback",
     *      host="{domain_website}",
     *      defaults={"_locale" = "%locale_website%", "domain_website" = "%domain_website%"},
     *      requirements={"_locale" = "%locale_website%|ru", "domain_website" = "%domain_website%"}
     * )
     */
    public function feedbackAction(Request $request)
    {
        $form = $this->createForm(new FeedbackType($this->_translator), $feedback = new Feedback);

        $form->handleRequest($request);

        if( $form->isSubmitted() )
        {
            if ($form->isValid()) {
                $this->_manager->flush();

                if ($this->_feedbackMailer->sendFeedback($feedback)) {
                    $this->_messages->markWebsiteFeedbackSuccess();
                } else {
                    $this->_messages->markWebsiteFeedbackError();
                }

                return $this->redirectToRoute('website_feedback');
            } else {
                $this->_messages->markWebsiteFeedbackErrors(
                    $this->getFormErrorMessages($form)
                );
            }
        }

        return $this->render('AppBundle:Website/State:feedback.html.twig', [
            'form' => $form->createView()
        ]);
    }
}