<?php

namespace AppBundle\Controller\Admin\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\Admin\Client\ClientType;
use AppBundle\Form\Admin\Client\ContractType;;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
                    'client_form' => $form->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
    }
    /**
     * @Route("/admin/client/{name}/contract/{cname}")
     * @Method("GET")
     */
    public function contractAction( $name, $cname )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->createQueryBuilder()->select( ['ct'] )
                ->from( 'AppBundle\Entity\Client\Contract', 'ct' )
		->join( 'AppBundle\Entity\Client\Client', 'c');
	$queryBuilder->where( $queryBuilder->expr()->eq( 'c.name', '?1' ))
		->andWhere( $queryBuilder->expr()->eq( 'ct.name', '?2' ))
	        ->setParameters([ 1 => $name, 2 => $cname ] );
        $query = $queryBuilder->getQuery();
        $clientContract = $query->getResult();
	if (is_array($clientContract)) {
		$clientContract = $clientContract[0];

	        $contractForm = $this->createForm( ContractType::class, $clientContract, [ 'action' => $this->generateUrl( 'app_admin_client_client_savecontract', ['name' => $name, 'cname' => $cname] ) ] );

        	return $this->render( 'admin/client/contract.html.twig', array(
		    'contract' => $clientContract,
                    'contract_form' => $contractForm->createView(),
                    'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                ) );
	}
    }

    /**
     * @Route("/admin/client/{name}/contract/{cname}")
     * @Method("POST")
     */
    public function saveContractAction( $name, $cname, Request $request )
    {
        $this->denyAccessUnlessGranted( 'ROLE_ADMIN', null, 'Unable to access this page!' );
        $em = $this->getDoctrine()->getManager();
        $response = new Response();
        $statuses = [];
        $statuses['statuses'] = $em->getRepository( 'AppBundle\Entity\Asset\AssetStatus' )->findAll();
        $form = $this->createForm( AssetStatusesType::class, $statuses, ['allow_extra_fields' => true] );
        $form->handleRequest( $request );
        if( $form->isSubmitted() && $form->isValid() )
        {
            $statuses = $form->getData();
            foreach( $statuses['statuses'] as $status )
            {
                $em->persist( $status );
            }
            $em->flush();
            $this->addFlash(
                    'notice', 'common.success' );
            $response = new RedirectResponse( $this->generateUrl( 'app_admin_asset_assetstatus_index', [], UrlGeneratorInterface::ABSOLUTE_URL ) );
            $response->prepare( $request );

            return $response->send();
        }
        else
        {
            return $this->render( 'admin/asset/asset-statuses.html.twig', array(
                        'statuses_form' => $form->createView(),
                        'base_dir' => realpath( $this->container->getParameter( 'kernel.root_dir' ) . '/..' ),
                    ) );
        }
    }

}
