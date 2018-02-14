<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13/02/18
 * Time: 23:24
 */

namespace ALC\WebServiceBundle\Entity\Users;

use ALC\EntityRestClientBundle\Annotations as Rest;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Rest\Resource("users")
 */
class Users
{
    /**
     * @Rest\Field("id")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("id")
     */
    private $idUsuario;

    /**
     * @Rest\Field("name")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $nombre;

    /**
     * @Rest\Field("username")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("username")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $nombreUsuario;

    /**
     * @Rest\Field("email")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("email")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $email;

    /**
     * @Rest\Field("address")
     * @Serializer\Type("array")
     * @Serializer\SerializedName("address")
     */
    private $direccion;

    /**
     * @Rest\Field("phone")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("phone")
     */
    private $telefono;

    /**
     * @Rest\Field("website")
     * @Serializer\Type("string")
     * @Serializer\SerializedName("website")
     */
    private $sitioWeb;

    /**
     * @Rest\Field("company")
     * @Serializer\Type("array")
     * @Serializer\SerializedName("company")
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