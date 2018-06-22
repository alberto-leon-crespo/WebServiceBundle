<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17/02/18
 * Time: 3:05
 */

namespace ALC\WebServiceBundle\Entity\Address;

use ALC\RestEntityManager\Annotations\Field;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Field(target="zipcode",type="string")
     */
    private $codigoPostal;

    /**
     * @Field(target="geo",type="ALC\WebServiceBundle\Entity\Geo\Geo")
     * @Assert\Valid()
     */
    private $geo;

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

    /**
     * @return mixed
     */
    public function getCodigoPostal()
    {
        return $this->codigoPostal;
    }

    /**
     * @param mixed $codigoPostal
     * @return Address
     */
    public function setCodigoPostal($codigoPostal)
    {
        $this->codigoPostal = $codigoPostal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeo()
    {
        return $this->geo;
    }

    /**
     * @param mixed $geo
     * @return Address
     */
    public function setGeo($geo)
    {
        $this->geo = $geo;
        return $this;
    }
}