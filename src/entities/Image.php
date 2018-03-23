<?php

namespace Entity;

class Image extends \Spot\Entity
{

    protected static $table  = 'images';

    public static function fields()
    {
        return [
            'id'            => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name'          => ['type' => 'string'],
            'product_id'    => ['type' => 'integer', 'index'    => true],
        ];
    }

    function getResizedName( $dim ) {

        $pathInfo = pathinfo( $this->name );

        return $pathInfo['filename'].".$dim.".$pathInfo['extension'];

    }
}
