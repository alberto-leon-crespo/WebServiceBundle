<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/02/18
 * Time: 19:52
 */

namespace ALC\WebServiceBundle\Entity\Geo;

use ALC\EntityRestClientBundle\Annotations\Field;

class Geo
{
    /**
     * @Field(target="lat",type="string")
     */
    private $latitud;

    /**
     * @Field(target="lng",type="string")
     */
    private $longitud;

    /**
     * @return mixed
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * @param mixed $latitud
     * @return Geo
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLongitud()
    {
        return $this->longitud;
    }

    /**
     * @param mixed $longitud
     * @return Geo
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;
        return $this;
    }
}