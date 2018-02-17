<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/02/18
 * Time: 6:16
 */

namespace ALC\EntityRestClientBundle\Listeners;


use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PreDeserialize implements EventSubscriberInterface
{
    private $fieldsMap;
    private $fieldsValues;
    private $fieldsTypes;

    public function __construct( SessionInterface $session )
    {
        $this->fieldsMap = $session->get('alc_entity_rest_client.handler.fieldsMap');
        $this->fieldsValues = $session->get('alc_entity_rest_client.handler.fieldsValues');
        $this->fieldsType = $session->get('alc_entity_rest_client.handler.fieldsType');
    }

    static public function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize'
            ),
        );
    }

    public function onSerializerPreDeserialize( PreDeserializeEvent $event ){

        $classType = $event->getType();

        if( !empty( $classType['name'] ) ){

            return $event;

        }

        $classMetadata = $event->getContext()->getMetadataFactory()->getMetadataForClass( $classType['name'] );
        $context = $event->getContext();
        $data = $event->getData();
        $visitor = $event->getVisitor();

        foreach( $this->fieldsMap as $originalFieldName => $targetFieldName ){

            if( array_key_exists( $originalFieldName, $this->fieldsMap ) ){

                $classMetadata->propertyMetadata[$originalFieldName]->serializedName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlEntryName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlCollectionSkipWhenEmpty = false;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlElementCData = false;

                $classMetadata->propertyMetadata[$originalFieldName]->type = array(
                    'name' => $this->fieldsTypes[ $targetFieldName ],
                    'params' => []
                );

            }

        }


        dump( $classMetadata );
        die();

        $context->pushClassMetadata( $classMetadata );

//        return $event;

    }
}