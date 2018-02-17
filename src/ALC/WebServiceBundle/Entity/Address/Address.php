<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/02/18
 * Time: 3:05
 */

namespace ALC\WebServiceBundle\Entity\Address;

use ALC\EntityRestClientBundle\Annotations\Field;

class Address
{
    /**
     * @Field(target="street",type="string")
     */
    private $calle;

    /**
     * @Field(target="suite",type="string")
     */
    private $piso;

    /**
     * @Field(target="city",type="string")
     */
    private $ciudad;

    /**
     * @return mixed
     */
    public function getCalle()
    {
        return $this->calle;
    }

    /**
     * @param mixed $calle
     * @return Address
     */
    public function setCalle($calle)
    {
        $this->calle = $calle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPiso()
    {
        return $this->piso;
    }

    /**
     * @param mixed $piso
     * @return Address
     */
    public function setPiso($piso)
    {
        $this->piso = $piso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param mixed $ciudad
     * @return Address
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
        return $this;
    }
}