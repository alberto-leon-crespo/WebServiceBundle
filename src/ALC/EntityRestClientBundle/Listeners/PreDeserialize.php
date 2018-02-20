<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/02/18
 * Time: 6:16
 */

namespace ALC\EntityRestClientBundle\Listeners;


use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use JMS\Serializer\Construction\UnserializeObjectConstructor;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class PreDeserialize implements EventSubscriberInterface
{
    private $fieldsMap;
    private $fieldsValues;
    private $fieldsType;
    private $annotationReader;
    private $attributesBag;
    private $doctrineObjectConstructor;
    private $anidateObject = false;

    public function __construct( RequestStack $requestStack, UnserializeObjectConstructor $doctrineObjectConstructor )
    {
        $this->attributesBag = $requestStack->getMasterRequest()->attributes;
        $this->fieldsMap = $this->attributesBag->get('alc_entity_rest_client.handler.fieldsMap');
        $this->fieldsValues = $this->attributesBag->get('alc_entity_rest_client.handler.fieldsValues');
        $this->fieldsType = $this->attributesBag->get('alc_entity_rest_client.handler.fieldsType');
        $this->annotationReader = new AnnotationReader();
        $this->doctrineObjectConstructor = $doctrineObjectConstructor;
    }

    static public function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.pre_deserialize',
                'method' => 'onserializerPreDeserialize'
            )
        );
    }

    public function onserializerPreDeserialize( PreDeserializeEvent $event ){

        $type = $event->getType();
        $context = $event->getContext();
        $data = $event->getData();
        $visitor = $event->getVisitor();

        $classMetadata = $event->getContext()->getMetadataFactory()->getMetadataForClass( $type['name'] );

        if( empty( $this->fieldsMap ) ){

            $this->readClassAnnotations( $type['name'] );
            $this->anidateObject = true;

        }

        foreach( $this->fieldsMap as $originalFieldName => $targetFieldName ){

            if( array_key_exists( $originalFieldName, $this->fieldsMap ) ){

                $classMetadata->propertyMetadata[$originalFieldName]->serializedName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlEntryName = $targetFieldName;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlCollectionSkipWhenEmpty = false;
                $classMetadata->propertyMetadata[$originalFieldName]->xmlElementCData = false;

                $classMetadata->propertyMetadata[$originalFieldName]->type = array(
                    'name' => $this->fieldsType[$originalFieldName],
                    'params' => []
                );

                unset( $this->fieldsMap[ $originalFieldName ] );

            }

        }

        if( $this->anidateObject ){

            return $this->doctrineObjectConstructor->construct( $visitor, $classMetadata, $data, $type, $context );

        }

        $context->pushClassMetadata( $classMetadata );

        return $event;

    }

    private function readClassAnnotations( $classNamespace ){

        $objClassInstanceReflection = new \ReflectionClass( $classNamespace );

        if( !empty( $objClassInstanceReflection->getProperties() ) ){

            foreach( $objClassInstanceReflection->getProperties() as $property ){

                $property->setAccessible( true );

                $arrPropertiesAnnotations = $this->annotationReader->getPropertyAnnotations( $property );

                foreach( $arrPropertiesAnnotations as $propertyAnnotation ){

                    if( get_class( $propertyAnnotation ) == "ALC\\EntityRestClientBundle\\Annotations\\Field" ){

                        $this->fieldsMap[ $property->getName() ] = $propertyAnnotation->getTarget();
                        $this->fieldsType[ $property->getName() ] = $propertyAnnotation->getType();

                        if( is_object( $classNamespace ) ){

                            $this->fieldsValues[ $property->getName() ] = $property->getValue( $classNamespace );

                        }else{

                            $this->fieldsValues[ $property->getName() ] = null;

                        }

                    }

                }

            }

        }

    }
}