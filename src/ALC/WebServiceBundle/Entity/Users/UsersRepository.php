<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 26/02/18
 * Time: 21:28
 */

namespace ALC\WebServiceBundle\Entity\Users;

use ALC\RestEntityManager\RestRepository;

class UsersRepository extends RestRepository
{
    public function listadoUsuariosWithVocalA(){

        $response = $this->get( 'users', array() );

        $arrUsers = $this->serializer->deserialize( $response->getBody()->getContents(), 'array<ALC\WebServiceBundle\Entity\Users\Users>', 'json' );

        foreach( $arrUsers as $key => $objUser ){

            if( mb_strpos( $objUser->getNombre(), 'a', null, 'utf8' ) === false ){

                unset( $arrUsers[$key] );

            }

        }

        return array_values( $arrUsers );

    }
}