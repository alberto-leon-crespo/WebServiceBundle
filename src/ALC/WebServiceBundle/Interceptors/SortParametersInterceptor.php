<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/06/18
 * Time: 23:24
 */

namespace ALC\WebServiceBundle\Interceptors;

use ALC\RestEntityManager\ParameterInterceptor;

class SortParametersInterceptor extends ParameterInterceptor
{
    public function parseSortFields($value)
    {
        $firstCharacter = $value[0];
        $fieldName = substr($value, 1);

        $order = null;

        if ($firstCharacter === '-') {
            $order = 'desc';
        } elseif ($firstCharacter === '+' || $firstCharacter === ' ') {
            $order = 'asc';
        }

        $arrFinalParams = $this->getMetadataClassReader()->matchEntityFieldsWithResourcesFieldsRecursive( [ $fieldName => $order ] );

        $arrayMatch = array();

        foreach( $arrFinalParams as $campoOrdenar => $metodoOrdenacion ){
            $arrayMatch['_sort'] = $campoOrdenar;
            $arrayMatch['_order'] = $metodoOrdenacion;
        };

        return $arrayMatch;
    }
}