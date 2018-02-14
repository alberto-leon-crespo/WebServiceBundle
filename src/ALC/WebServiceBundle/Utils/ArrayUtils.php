<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/02/18
 * Time: 2:08
 */

namespace ALC\WebServiceBundle\Utils;


abstract class ArrayUtils
{
    public static function recursiveObjectToArray( $object ){

        if( is_array( $object ) ){

            array_walk_recursive( $object, function( &$value ){

                if( is_object( $value ) ){

                    $value = self::recursiveObjectToArray( $value );

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

                        $itemArray = self::recursiveObjectToArray( $item );
                        $public[$name][] = $itemArray;

                    }else{

                        $public[$name][] = $item;

                    }
                }

            }else if( is_object($value) ){

                $public[$name] = self::recursiveObjectToArray( $value );

            }else{

                $public[$name] = $value;

            }
        }

        return $public;
    }
}