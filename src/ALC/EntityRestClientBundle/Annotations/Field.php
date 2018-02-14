<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/02/18
 * Time: 23:46
 */

namespace ALC\EntityRestClientBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Annotation\Target("METHOD")
 */
class Field
{
    private $name;

    public function __construct( $options )
    {
        $this->name = !empty( $options['name'] ) ? $options['name'] : $options['value'];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}