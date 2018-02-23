<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19/02/18
 * Time: 20:49
 */

namespace ALC\WebServiceBundle\Entity\Companies;

use ALC\RestEntityManager\Annotations\Field;

class Companies
{
    /**
     * @Field(target="name",type="string")
     */
    private $nombre;

    /**
     * @Field(target="catchPhrase",type="string")
     */
    private $fraseCaptacion;

    /**
     * @Field(target="bs",type="string")
     */
    private $bs;

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     * @return Companies
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFraseCaptacion()
    {
        return $this->fraseCaptacion;
    }

    /**
     * @param mixed $fraseCaptacion
     * @return Companies
     */
    public function setFraseCaptacion($fraseCaptacion)
    {
        $this->fraseCaptacion = $fraseCaptacion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBs()
    {
        return $this->bs;
    }

    /**
     * @param mixed $bs
     * @return Companies
     */
    public function setBs($bs)
    {
        $this->bs = $bs;
        return $this;
    }
}