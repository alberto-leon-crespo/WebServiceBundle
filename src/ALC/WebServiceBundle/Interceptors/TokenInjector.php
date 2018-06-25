<?php
/**
 * Created by PhpStorm.
 * User: aleon
 * Date: 24/06/2018
 * Time: 21:57
 */

namespace ALC\WebServiceBundle\Interceptors;

use ALC\RestEntityManager\Interceptors\RequestInterceptor;
use GuzzleHttp\Event\BeforeEvent;

class TokenInjector extends RequestInterceptor
{
    public function injectToken(BeforeEvent $event, array $arrManagerConfig){

        $apiKey = $arrManagerConfig['custom_params']['secret_api_key'];

        $event->getRequest()->setHeader('X-Api-Key', $apiKey);

        return $event;
    }
}