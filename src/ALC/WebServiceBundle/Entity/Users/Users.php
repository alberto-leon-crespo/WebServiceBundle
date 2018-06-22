<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13/02/18
 * Time: 23:24
 */

namespace ALC\WebServiceBundle\Entity\Users;

use ALC\RestEntityManager\Annotations as Rest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Rest\Resource("users")
 * @Rest\Repository("ALC\WebServiceBundle\Entity\Users\UsersRepository")
 * @Rest\Headers({"content-type":"application/json"})
 */
class Users
{
    /**
     * @Rest\Id()
     * @Rest\Field(target="id",type="integer")
     */
    private $idUsuario;

    /**
     * @Rest\Field(target="name",type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @Rest\Field(target="username",type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $nombreUsuario;

    /**
     * @Rest\Field(target="email",type="string")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @Rest\Field(target="address",type="ALC\WebServiceBundle\Entity\Address\Address")
     */
    private $direccion;

    /**
     * @Rest\Field(target="phone",type="string")
     */
    private $telefono;

    /**
     * @Rest\Field(target="website",type="string")
     */
    private $sitioWeb;

    /**
     * @Rest\Field(target="company",type="ALC\WebServiceBundle\Entity\Companies\Companies")
     */
    private $compania;

    /**
     * @return mixed
     */
    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    /**
     * @param mixed $idUsuario
     * @return Users
     */
    public function setIdUsuario($idUsuario)
    {
        $this->idUsuario = $idUsuario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     * @return Users
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * @param mixed $nombreUsuario
     * @return Users
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     * @return Users
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param mixed $telefono
     * @return Users
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSitioWeb()
    {
        return $this->sitioWeb;
    }

    /**
     * @param mixed $sitioWeb
     * @return Users
     */
    public function setSitioWeb($sitioWeb)
    {
        $this->sitioWeb = $sitioWeb;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCompania()
    {
        return $this->compania;
    }

    /**
     * @param mixed $compania
     * @return Users
     */
    public function setCompania($compania)
    {
        $this->compania = $compania;
        return $this;
    }
}