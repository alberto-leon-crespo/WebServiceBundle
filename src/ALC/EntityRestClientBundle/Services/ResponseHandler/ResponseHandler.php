<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/02/18
 * Time: 11:52
 */

namespace ALC\EntityRestClientBundle\Services\ResponseHandler;

use ALC\EntityRestClientBundle\Utils\ArrayToXML;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Inflector\Inflector;

class ResponseHandler
{
    public function createResponse(ViewHandler $handler, View $view, Request $request, $format){

        if( $format == 'json' ){

            $response = $view->getResponse();

            $data = $view->getData();

            $data = json_encode( $this->recursiveObjectToArray( $data ) );

            $response->setContent( $data );

            return $response;

        }else if( $format == 'xml' ){

            $response = $view->getResponse();

            $data = $view->getData();

            $data = $this->recursiveObjectToArray( $data );

            $procesedEntity = $request->attributes->get('alc_entity_rest_client.procesedEntity' );

            $procesedEntityNameSpaceParts = explode( "\\", $procesedEntity );

            $objectName = array_pop( $procesedEntityNameSpaceParts );

            $objectNameSingularized = Inflector::singularize( $objectName );

            $xml = new ArrayToXML( strtolower( $objectName ), strtolower( $objectNameSingularized ) );

            $xml->createNode( $data );

            $response->setContent( (string)$xml );

            return $response;

        }

    }

    private function recursiveObjectToArray( $object ){

        if( is_array( $object ) ){

            array_walk_recursive( $object, function( &$value ){

                if( is_object( $value ) ){

                    $value = $this->recursiveObjectToArray( $value );

                }

            });

            return $object;

        }

        $reflectionClass = new \ReflectionClass( $object );

        $properties = $reflectionClass->getProperties();

        $public = array();

        foreach( $properties as $property ){

            $property->setAccessible( true );

            $value = $property->getValue( $object );
            $name = $property->getName();

            if( is_array( $value ) ){

                $public[$name] = [];

                foreach( $value as $item ){

                    if( is_object( $item ) ){

                        $itemArray = $this->recursiveObjectToArray( $item );
                        $public[$name][] = $itemArray;

                    }else{

                        $public[$name][] = $item;

                    }
                }

            }else if( is_object($value) ){

                if( empty( $this->recursiveObjectToArray( $value ) ) ){

                    $public[$name] = get_object_vars( $value );

                }else{

                    $public[$name] = $this->recursiveObjectToArray( $value );

                }

            }else{

                $public[$name] = $value;

            }
        }

        return $public;
    }
}