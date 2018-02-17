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
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\NullAwareVisitorInterface;

class PreDeserialize implements EventSubscriberInterface
{
    private $fieldsMap;
    private $fieldsValues;
    private $fieldsType;

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

        $type = $event->getType();
        $context = $event->getContext();
        $data = $event->getData();
        $visitor = $event->getVisitor();

        $classMetadata = $event->getContext()->getMetadataFactory()->getMetadataForClass( $type['name'] );

        foreach( $this->fieldsMap as $originalFieldName => $targetFieldName ){

            if( array_key_exists( $originalFieldName, $this->fieldsMap ) ){

                $classMetadata->propertyMetadata[$originalFieldName]->serializedName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlEntryName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlCollectionSkipWhenEmpty = false;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlElementCData = false;

                $classMetadata->propertyMetadata[$originalFieldName]->type = array(
                    'name' => $this->fieldsType[ $originalFieldName ],
                    'params' => []
                );

            }

        }

        $context->pushClassMetadata( $classMetadata );

        return $event;

    }
}