<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/02/18
 * Time: 0:49
 */

namespace ALC\WebServiceBundle\Controller;

use ALC\WebServiceBundle\Utils\ArrayUtils;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends FOSRestController
{
    public function getUsersAction(Request $objRequest){

        $arrFilters = $objRequest->query->all();

        $objEntityManager = $this->get('alc_entity_rest_client.handler')->getManager();

        $objUsersRespository = $objEntityManager->getRepository('ALCWebServiceBundle:Users\Users');

        if( empty( $arrFilters ) ){

            $arrUsers = $objUsersRespository->findAll( 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>' );

        }else{

            $arrUsers = $objUsersRespository->findOneBy( $arrFilters, 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>' );

        }

        return ArrayUtils::recursiveObjectToArray( $arrUsers );

    }

    public function getUserAction(Request $objRequest, $userId ){

        $objEntityManager = $this->get('alc_entity_rest_client.handler')->getManager();

        $objUsersRespository = $objEntityManager->getRepository('ALCWebServiceBundle:Users\Users');

        $arrUser = $objUsersRespository->find( $userId, 'object', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        return ArrayUtils::recursiveObjectToArray( $arrUser );

    }

    public function postUsersAction(Request $objRequest){

        $objUser =
            $this
                ->get('alc_entity_rest_client.request_entity_decoder')
                ->decodeAndSerializeRequest( 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        $objValidationErrors = $this->get('validator')->validate( $objUser );

        if( $objValidationErrors->count() > 0 ) {

            /**
             * @var $validacion \
             */
            foreach ($objValidationErrors as $validacion) {

                $arrValidationErrors['errors'][$validacion->getPropertyPath()] = $validacion->getMessage();

            }

            return ArrayUtils::recursiveObjectToArray( $arrValidationErrors );
        }

        $em = $this->get('alc_entity_rest_client.handler')->getManager();

        /**
         * @var $objUser \ALC\WebServiceBundle\Entity\Users\Users
         */
        $objUser = $em->persist( $objUser, 'object', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        $em->flush();

        return $this->redirectToRoute( 'get_user', [ "_locale" => $objRequest->getLocale(), "userId" => $objUser->getIdUsuario() ] );

    }
}