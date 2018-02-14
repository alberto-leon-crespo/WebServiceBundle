<?php

namespace ALC\WebServiceBundle\Controller;

use ALC\WebServiceBundle\Utils\Array2XML;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FOSRestController
{
    public function getDefaultAction( Request $objRequest )
    {
        $objUsersRespository = $this->get('alc_entity_rest_client.handler')->getManager()->getRepository('ALCWebServiceBundle:Users\Users');

        dump( $objUsersRespository->find( 1, 'object', 'ALC\\WebServiceBundle\\Entity\\Users\\Users' ) );
        dump( $objUsersRespository->findOneBy( ['telefono'=>"1-770-736-8031 x56442", "nombreUsuario"=>"Bret"], 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>' ) );
        dump( $objUsersRespository->findOneBy( ['telefono'=>"1-770-736-8031 x56442", "nombreUsuario"=>"Bret"], 'object', 'array<ALC\\WebServiceBundle\\Entity\\Users\\Users>' ) );
        die();

        $arrDatosObj = $this->get('jms_serializer')->toArray( $obj );

        return new View( $arrDatosObj, 200 );
    }

    public function postDefaultAction( Request $objRequest ){

        $objDatos = $this->get('jms_serializer')->deserialize( $objRequest->getContent(), 'ALC\\WebServiceBundle\\Entity\\Object', $objRequest->getRequestFormat() );

        $objValidaciones = $this->get('validator')->validate( $objDatos );

        if( $objValidaciones->count() ){

            /**
             * @var $validacion \
             */
            foreach( $objValidaciones as $validacion ){

                $arrErrores['errores'][ $validacion->getPropertyPath() ] = $validacion->getMessage();

            }

            if( $objRequest->getRequestFormat() == "xml" ){

                $xml = Array2XML::createXML( 'response', $arrErrores );

                return new Response( $xml->saveXML(), 400 );

            }else{

                return View::create( $arrErrores, 400 );

            }

        }

        die();

    }
}
