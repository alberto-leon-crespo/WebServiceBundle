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
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends FOSRestController
{
    public function getUsersAction(Request $objRequest){

        $arrFilters = $objRequest->query->all();

        $objEntityManager = $this->get('alc_rest_entity_manager.handler')->getManager();

        $objUsersRespository = $objEntityManager->getRepository('ALCWebServiceBundle:Users\Users');

        if( empty( $arrFilters ) ){

            $arrUsers = $objUsersRespository->findAll( 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>', false );

        }else{

            $arrUsers = $objUsersRespository->findBy( $arrFilters, 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>', false );

        }

        return $arrUsers;

    }

    public function getUserAction(Request $objRequest, $userId ){

        $objEntityManager = $this->get('alc_rest_entity_manager.handler')->getManager();

        $objUsersRespository = $objEntityManager->getRepository('ALCWebServiceBundle:Users\Users');

        $arrUser = $objUsersRespository->find( $userId, 'object', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        return $arrUser;

    }

    public function postUsersAction(Request $objRequest){

        $objUser = $this->get('fos_rest.serializer')->deserialize( $objRequest->getContent(), 'json', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        $objValidationErrors = $this->get('validator')->validate( $objUser );

        if( $objValidationErrors->count() > 0 ) {

            $arrValidationErrors = [];

            /**
             * @var $validacion \
             */
            foreach ($objValidationErrors as $validacion) {

                $arrValidationErrors['errors'][$validacion->getPropertyPath()] = $validacion->getMessage();

            }

            return View::create( $arrValidationErrors, 200 );
        }

        $em = $this->get('alc_rest_entity_manager.handler')->getManager();

        /**
         * @var $objUser \ALC\WebServiceBundle\Entity\Users\Users
         */
        $objUser = $em->persist( $objUser, 'object', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' );

        return $this->redirectToRoute( 'get_user', [ "_locale" => $objRequest->getLocale(), "userId" => $objUser->getIdUsuario() ] );

    }
}