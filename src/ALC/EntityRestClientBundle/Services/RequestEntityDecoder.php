<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/02/18
 * Time: 3:00
 */

namespace ALC\EntityRestClientBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;

class RequestEntityDecoder
{
    private $request;
    private $serializer;

    public function __construct( RequestStack $objRequest )
    {
        $this->serializer = SerializerBuilder::create()->setPropertyNamingStrategy( new IdenticalPropertyNamingStrategy() )->build();
        $this->request = $objRequest->getMasterRequest();
    }

    public function decodeAndSerializeRequest( $class ){

        $obj = $this->serializer->deserialize( $this->request->getContent(), $class, $this->request->getRequestFormat() );

        return $obj;
    }
}