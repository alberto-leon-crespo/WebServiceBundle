<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/02/18
 * Time: 19:54
 */

namespace ALC\EntityRestClientBundle\Services\RestEntityHandler;

use ALC\EntityRestClientBundle\RestManager;
use ALC\EntityRestClientBundle\Services\RestEntityHandler\Exception\InvalidParamsException;
use GuzzleHttp\Message\Response;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use ALC\EntityRestClientBundle\Services\RestEntityHandler\Exception\RunTimeException;

class RestEntityHandler extends RestManager
{
    private $bundleConfig;
    private $session;
    private $bundles;
    private $serializer;
    private $attibutesBag;
    private $annotationReader;
    private $path;
    private $fieldsMap;
    private $fieldsType;
    private $fieldsValues;
    private $entityIdValue;
    private $entityIdFieldName;

    private $headers = array(
        'content-type' => 'application/json'
    );

    private $deserilizationFormats = array(
        'application/json' => 'json',
        'application/xml' => 'xml',
        'application/html' => 'json'
    );

    private $serializationFormats = array(
        'application/json' => 'json',
        'application/xml' => 'xml',
        'application/html' => 'json'
    );

    /**
     * RestEntityHandler constructor.
     * @param array $config
     * @param SessionInterface $session
     * @param array $bundles
     * @param Serializer $serializer
     * @param RequestStack $requestStack
     */
    public function __construct( array $config, SessionInterface $session, array $bundles, Serializer $serializer, RequestStack $requestStack ){

        parent::__construct( $config['managers'][ $config['default_manager'] ], $session );

        $this->bundleConfig = $config;
        $this->session = $session;
        $this->bundles = $bundles;
        $this->serializer = $serializer;
        $this->attibutesBag = $requestStack->getMasterRequest()->attributes;
        $this->annotationReader = new AnnotationReader();

        return $this;

    }

    /**
     * @param null $strManagerName
     * @return $this
     */
    public function getManager( $strManagerName = null ){

        if( empty( $strManagerName ) ){

            $strManagerName = $this->bundleConfig['default_manager'];

        }

        if( !array_key_exists( $strManagerName, $this->bundleConfig['managers'] ) ){

            throw new InvalidParamsException( 400, "Restmanager that you can try to load <$strManagerName> is not defined under alc_entity_rest_client.managers" );

        }

        $this->config = $this->bundleConfig['managers'][$strManagerName];

        return $this;
    }

    /**
     * @param $strPersistenceObjectName
     * @return $this
     */
    public function getRepository( $strPersistenceObjectName ){

        $strRepositoryPath = explode(":", $strPersistenceObjectName );

        $bundleName = $strRepositoryPath[0];
        $entityPath = $strRepositoryPath[1];

        if( !array_key_exists( $bundleName, $this->bundles ) ){

            throw new InvalidParamsException( 400, "Bundle <$bundleName> doesn't exist loaded  in symfony configuration." );

        }

        $classNamespaceParts = explode("\\", $this->bundles[$bundleName] );

        array_pop( $classNamespaceParts );

        $classNamespace = implode("\\", $classNamespaceParts );

        $classNamespace = $classNamespace . "\\Entity\\" . $entityPath;

        if( !class_exists( $classNamespace ) ){

            throw new InvalidParamsException( 400, "Class $classNamespace doesn't exist." );

        }

        $this->readClassAnnotations( $classNamespace );

        return $this;

    }

    public function find( $id, $format = 'json', $objClass = null )
    {
        $response = $this->get( $this->path . "/" . $id, array(), $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    public function findBy( array $arrFilters, $format = 'json', $objClass = null )
    {
        $arrParams = $this->matchEntityFieldsWithResourceFields( $arrFilters );

        $response = $this->get( $this->path, $arrParams, $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    public function findOneBy( array $arrFilters, $format = 'json', $objClass = null )
    {
        $arrParams = $this->matchEntityFieldsWithResourceFields( $arrFilters );

        $response = $this->get( $this->path, $arrParams, $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass )[0];
    }

    public function findAll( $format = 'json', $objClass = null )
    {
        $response = $this->get( $this->path, array(), $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    /**
     * {@inheritdoc}
     */
    public function persist( $object, $format = 'json', $objClass = null )
    {
        $this->readClassAnnotations( $object );

        $arrHeaders = array();
        $serializationFormat = 'json';

        foreach( $this->headers as $header => $value ){

            if( strpos( 'content-type', $header ) !== false  ||  strpos( 'Content-type', $header ) !== false || strpos( 'CONTENT-TYPE', $header ) !== false ){

                $headers[$header] = $value;

                $serializationFormat = $this->serializationFormats[$value];

                if( !array_key_exists( $value, $this->serializationFormats ) ){

                    throw new RunTimeException(400, "Content type serialization is not suported. Suported types are " . implode( ",", $this->serializationFormats ) );

                }

            }

        }

        $payload = $this->serializer->serialize( $object, $serializationFormat );

        if( $this->entityIdValue !== null ){

            $response = $this->put( $this->path . '/' . $this->entityIdValue, $payload, $arrHeaders );

        }else{

            $response = $this->post( $this->path, $payload, $arrHeaders );

        }

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    /**
     * {@inheritdoc}
     */
    public function remove( $object, $format = 'json', $objClass = null )
    {
        $this->readClassAnnotations( $object );

        $arrHeaders = array();

        foreach( $this->headers as $header => $value ){

            if( strpos( 'content-type', $header ) !== false  ||  strpos( 'Content-type', $header ) !== false || strpos( 'CONTENT-TYPE', $header ) !== false ){

                $headers[$header] = $value;

                if( !array_key_exists( $value, $this->serializationFormats ) ){

                    throw new RunTimeException(400, "Content type serialization is not suported. Suported types are " . implode( ",", $this->serializationFormats ) );

                }

            }

        }

        $response = $this->detete( $this->path . '/' . $this->entityIdValue, $arrHeaders );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    public function merge( $object, $format = 'json', $objClass = null ){

        $this->readClassAnnotations( $object );

        $arrHeaders = array();
        $serializationFormat = 'json';

        foreach( $this->headers as $header => $value ){

            if( strpos( 'content-type', $header ) !== false  ||  strpos( 'Content-type', $header ) !== false || strpos( 'CONTENT-TYPE', $header ) !== false ){

                $headers[$header] = $value;

                $serializationFormat = $this->serializationFormats[$value];

                if( !array_key_exists( $value, $this->serializationFormats ) ){

                    throw new RunTimeException(400, "Content type serialization is not suported. Suported types are " . implode( ",", $this->serializationFormats ) );

                }

            }

        }

        $response = $this->find( $this->entityIdValue, 'json' );

        $payload = $this->serializer->serialize( $object, $serializationFormat );

        $idFieldName = $this->entityIdFieldName;
        $keyExist = false;

        array_walk_recursive( $response, function( $key ) use ( $idFieldName, &$keyExist ){

            if( $key == $idFieldName ){

                $keyExist = true;

            }

        });

        if( $keyExist ){

            $response = $this->put( $this->path, $payload, $arrHeaders );

        }else{

            $response = $this->post( $this->path, $payload, $arrHeaders );

        }

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    /**
     * {@inheritdoc}
     */
    public function refresh( &$object, $format = 'json', $objClass = null )
    {
        $this->readClassAnnotations( $object );

        $refreshingData = $this->find( $this->entityIdValue, $format, $objClass );

        $idFieldName = $this->entityIdFieldName;
        $keyExist = false;

        array_walk_recursive( $response, function( $key ) use ( $idFieldName, &$keyExist ){

            if( $key == $idFieldName ){

                $keyExist = true;

            }

        });

        if( $keyExist ){

            $object = $refreshingData;

            return $object;

        }

        return $object;
    }

    private function readClassAnnotations( $classNamespace ){

        $objClassInstanceReflection = new \ReflectionClass( $classNamespace );

        if( !empty( $this->annotationReader->getClassAnnotations( $objClassInstanceReflection ) ) ){

            foreach( $this->annotationReader->getClassAnnotations( $objClassInstanceReflection ) as $annotation ){

                if( get_class( $annotation ) == "ALC\\EntityRestClientBundle\\Annotations\\Resource" ){

                    $this->path = $annotation->getValue();

                }

                if( get_class( $annotation ) == "ALC\\EntityRestClientBundle\\Annotations\\Headers" ){

                    $this->headers = $annotation->getValues();

                }

            }

        }

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

                    if( get_class( $propertyAnnotation ) == "ALC\\EntityRestClientBundle\\Annotations\\Id" ){

                        if( is_object( $classNamespace ) ){

                            $this->entityIdValue = $property->getValue( $classNamespace );

                        }

                        $this->entityIdFieldName = $property->getName();

                    }

                }

            }

            $this->attibutesBag->set( 'alc_entity_rest_client.handler.fieldsMap', $this->fieldsMap );
            $this->attibutesBag->set( 'alc_entity_rest_client.handler.fieldsType', $this->fieldsType );
            $this->attibutesBag->set( 'alc_entity_rest_client.handler.fieldsValues', $this->fieldsValues );
        }

    }

    /**
     * @param Response $response
     * @param string $format
     * @param null $objClass
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    private function deserializeResponse( Response $response, $format = 'json', $objClass = null ){

        if( $format == 'json' ){

            return $response->json();

        }else if( $format == 'xml' ){

            return $response->xml();

        }else if( $format = 'object' && $objClass !== null ){

            $detectedFormat = false;

            foreach( $this->deserilizationFormats as $header => $format ){

                if( strpos( $response->getHeader('content-type'), $header ) !== false ){

                    $detectedFormat = true;
                    $deserializeFormat = $this->deserilizationFormats[$header];

                }

            }

            if( $detectedFormat === false ){

                throw new RunTimeException(400, "Unserialized format <" . $response->getHeader('content-type') . "> is not supported.", null, $response->getHeaders(), 0 );

            }

            $className = str_replace( "array", "", $objClass );
            $className = str_replace( "<", "", $className );
            $className = str_replace( ">", "", $className );

            $this->readClassAnnotations( $className );

            $this->attibutesBag->set( 'alc_entity_rest_client.procesedEntity', $className );

            return $this->serializer->deserialize( (string)$response->getBody(), $objClass, $deserializeFormat );

        }

    }

    private function matchEntityFieldsWithResourceFields( $array ){

        $arrayMatchedParams = array();

        foreach( $array as $propertyName => $value ){

            if( array_key_exists( $propertyName, $this->fieldsMap ) ){

                $arrayMatchedParams[ $this->fieldsMap[ $propertyName ] ] = $value;

            }

        }

        return $arrayMatchedParams;
    }
}