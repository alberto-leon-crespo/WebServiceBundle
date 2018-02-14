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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use ALC\EntityRestClientBundle\Services\RestEntityHandler\Exception\RunTimeException;

class RestEntityHandler extends RestManager
{
    private $bundleConfig;
    private $session;
    private $bundles;
    private $serializer;
    private $annotationReader;
    private $path;
    private $headers = array();
    private $fieldsMap;
    private $deserilizationFormats = array(
        'application/json' => 'json',
        'application/xml' => 'xml',
        'application/html' => 'xml'
    );

    /**
     * RestEntityHandler constructor.
     * @param array $config
     * @param SessionInterface $session
     */
    public function __construct( array $config, SessionInterface $session, array $bundles, Serializer $serializer ){

        parent::__construct( $config['managers'][ $config['default_manager'] ], $session );

        $this->bundleConfig = $config;
        $this->session = $session;
        $this->bundles = $bundles;
        $this->serializer = $serializer;

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

            throw new InvalidParamsException( 400, "Restmanager that you can try to load <$strManagerName> is not defined under alc_entity_rest_client.managers", null, array(), __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__ );

        }

        $this->config = $this->bundleConfig['managers'][$strManagerName];

        return $this;
    }

    /**
     * @param $strPersistenceObjectName
     * @return $this
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    public function getRepository( $strPersistenceObjectName ){

        $strRepositoryPath = explode(":", $strPersistenceObjectName );

        $bundleName = $strRepositoryPath[0];
        $entityPath = $strRepositoryPath[1];

        if( !array_key_exists( $bundleName, $this->bundles ) ){

            throw new InvalidParamsException( 400, "Bundle <$bundleName> doesn't exist loaded  in symfony configuration.", null, array(), __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__ );

        }

        $classNamespaceParts = explode("\\", $this->bundles[$bundleName] );

        array_pop( $classNamespaceParts );

        $classNamespace = implode("\\", $classNamespaceParts );

        $classNamespace = $classNamespace . "\\Entity\\" . $entityPath;

        if( !class_exists( $classNamespace ) ){

            throw new InvalidParamsException( 400, "Class $classNamespace doesn't exist.", null, array(), __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__ );

        }

        $this->readClassAnnotations( $classNamespace );

        return $this;

    }

    public function find( $id, $format = 'json', $objClass = null )
    {
        $response = $this->get( $this->path . "/" . $id, array(), $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    public function findOneBy( array $arrFilters, $format = 'json', $objClass = null )
    {
        $arrParams = $this->matchEntityFieldsWithResourceFields( $arrFilters );

        $response = $this->get( $this->path, $arrParams, $this->headers );

        return $this->deserializeResponse( $response, $format, $objClass );
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

        $payload = $this->serializer->serialize( $object, 'json' );

        $response = $this->post( $this->path, $payload );

        return $this->deserializeResponse( $response, $format, $objClass );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        return $this->wrapped->remove($object);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($object)
    {
        return $this->wrapped->merge($object);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
    {
        return $this->wrapped->clear($objectName);
    }

    /**
     * {@inheritdoc}
     */
    public function detach($object)
    {
        return $this->wrapped->detach($object);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($object)
    {
        return $this->wrapped->refresh($object);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->path = null;
        $this->headers = null;
        $this->fieldsMap = null;

        return $this;
    }

    private function readClassAnnotations( $classNamespace ){

        $objClassInstanceReflection = new \ReflectionClass( $classNamespace );
        $this->annotationReader = new AnnotationReader();

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

                $arrPropertiesAnnotations = $this->annotationReader->getPropertyAnnotations( $property );

                foreach( $arrPropertiesAnnotations as $propertyAnnotation ){

                    if( get_class( $propertyAnnotation ) == "ALC\\EntityRestClientBundle\\Annotations\\Field" ){

                        $property->setAccessible( true );

                        $this->fieldsMap[ $property->getName() ] = $propertyAnnotation->getName();

                    }

                }

            }

        }

    }

    /**
     * @param Response $response
     * @param string $format
     * @param null $objClass
     * @return array|\JMS\Serializer\scalar|mixed|object
     */
    private function deserializeResponse( Response $response, $format = 'json', $objClass = null ){

        if( $response->getStatusCode() )

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