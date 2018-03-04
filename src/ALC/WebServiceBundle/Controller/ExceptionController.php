<?php
/**
 * Created by PhpStorm.
 * User: master
 * Date: 4/03/18
 * Time: 15:51
 */

namespace ALC\WebServiceBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends FOSRestController
{
    public function showAction(Request $objRequest,\Exception $exception){

        $responseArray = array(
            'uri' => $objRequest->getUri(),
            'method' => $objRequest->getRealMethod(),
            'headers' => $objRequest->headers->all(),
            'queryParams' => $objRequest->query->all(),
            'requestParams' => $objRequest->request->all(),
            'requestBody' => $objRequest->getContent(),
            'session' => $objRequest->getSession()->all(),
            'reference' => uniqid(),
            'debug' => array(
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'trace' => $exception->getTrace()
            )
        );

        foreach( $responseArray['debug']['trace'] as $traceIndex => $trace ){

            unset( $responseArray['debug']['trace'][$traceIndex]['args'] );

        }

        $requestFormat = 'json';

        if( $objRequest->headers->get('content-type') == "application/json" ){

            $requestFormat = 'json';

        }else if( $objRequest->headers->get('content-type') == "application/xml" ){

            $requestFormat = 'xml';

        }else if( $objRequest->headers->get('content-type') == "application/html" ){

            $requestFormat = 'xml';

        }else if( $objRequest->headers->get('content-type') == "text/html" ){

            $requestFormat = 'xml';

        }

        $view = View::create( $responseArray, 200 );

        $view->setFormat( $requestFormat );

        return $view;
    }
}