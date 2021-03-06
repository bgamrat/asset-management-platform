<?php

Namespace App\Controller\Admin\Client;

use App\Form\Admin\Client\ClientType;
use App\Form\Admin\Client\ContractType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Description of AssetController
 *
 * @author bgamrat
 */
class ClientController extends Controller
{

    /**
     * @Route("/admin/client/client")
     * @Method("GET")
     */
    public function indexAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $form = $this->createForm( ClientType::class, null, [] );

        return $this->render( 'admin/client/client.html.twig', array(
                    'client_form' => $form->createView()) );
    }

    /**
     * @Route("/admin/contract/{id}/equipment")
     * @Method("GET")
     */
    public function viewContractEquipmentAction( $id )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $contract = $em->getRepository( 'App\Entity\Client\Contract' )->find( $id );
        if( $contract !== null )
        {
            return $this->render( 'admin/client/contract-equipment.html.twig', array(
                        'contract' => $contract,
                        'no_hide' => true,
                        'omit_menu' => true )
                     );
        }
    }

    /**
     * @Method("GET")
     * @Route("/admin/client/{name}/contract/{cname}")
     */
    public function contractAction( Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $name = $request->get('name');
        $cname = $request->get('cname');

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['ct'] )
                ->from( 'App\Entity\Client\Contract', 'ct' )
                ->join( 'App\Entity\Client\Client', 'c' );
        $queryBuilder->where( $queryBuilder->expr()->eq( 'c.name', '?1' ) )
                ->andWhere( $queryBuilder->expr()->eq( 'ct.name', '?2' ) )
                ->setParameters( [ 1 => $name, 2 => $cname] );
        $query = $queryBuilder->getQuery();
        $clientContract = $query->getResult();
        if( is_array( $clientContract ) )
        {
            $clientContract = $clientContract[0];

            $form = $this->createForm( ContractType::class, $clientContract, [ 'action' => $this->generateUrl( 'app_admin_client_client_contract', ['name' => $name, 'cname' => $cname] )] );

            $form->handleRequest( $request );
            if( $form->isSubmitted() && $form->isValid() )
            {
                $clientContract = $form->getData();
                $em->persist( $clientContract );
                $em->flush();
                $this->addFlash(
                        'notice', 'common.success' );
                $response = new RedirectResponse( $this->generateUrl( 'app_admin_client_client_contract', ['name' => $name, 'cname' => $cname], UrlGeneratorInterface::ABSOLUTE_URL ) );
                $response->prepare( $request );

                return $response->send();
            }
            else
            {
                return $this->render( 'admin/client/contract.html.twig', array(
                            'contract' => $clientContract,
                            'contract_form' => $form->createView()) );
            }
        }
    }

}
