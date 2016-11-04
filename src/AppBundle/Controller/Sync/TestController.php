<?php
// AppBundle/Controller/Sync/TestController.php
namespace AppBundle\Controller\Sync;

use Exception;

use DateTime;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\DiExtraBundle\Annotation as DI;

use AppBundle\Entity\TestEntity;

class TestController extends Controller
{
    /** @DI\Inject("doctrine.orm.entity_manager") */
    private $_manager;

    /**
     * @Method({"POST"})
     * @Route(
     *      "/banking_machines/{serial}/sync",
     *      name = "sync_post_banking_machines_sync",
     *      host = "{domain_sync_v1}",
     *      schemes = {"http"},
     *      defaults = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" },
     *      requirements = { "_locale" = "%locale%", "domain_sync_v1" = "%domain_sync_v1%" }
     * )
     */
    public function getVendingMachinesSync(Request $request, $serial)
    {
        $requestContent = $request->getContent();

        if( $requestContent )
        {
            $testEntity = (new TestEntity())
                ->setSyncDate(new DateTime())
                ->setSyncJson($requestContent)
            ;
            $this->_manager->persist($testEntity);
            $this->_manager->flush();
        }

        return new Response(json_encode($serial, JSON_UNESCAPED_UNICODE), 200);
    }
}
