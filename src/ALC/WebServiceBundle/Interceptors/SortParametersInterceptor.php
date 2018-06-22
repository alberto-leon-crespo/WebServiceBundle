<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/06/18
 * Time: 23:24
 */

namespace ALC\WebServiceBundle\Interceptors;

use ALC\RestEntityManager\ParamInterceptor;

class SortParametersInterceptor extends ParamInterceptor
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

        return array(
            '_sort' => $this->arrFieldsValues[$fieldName],
            '_order' => $order
        );
    }
}