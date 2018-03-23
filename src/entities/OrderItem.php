<?php
namespace Entity;

use 
    \Spot\Entity,
    \Spot\Mapper,
    \Spot\MapperInterface,
    \Spot\EntityInterface,
    \Spot\EventEmitter
;

use Util\Util;

class OrderItem extends \Spot\Entity
{

    //protected static $mapper    = 'Mapper\OrderItem';
    protected static $table     = 'order_items';

    public static function fields()
    {
        return [
            'id'                => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'order_id'          => ['type' => 'integer', 'index' => true, 'required' => true],
            'product_id'        => ['type' => 'integer', 'index' => true, 'required' => true],
            'price'             => ['type' => 'decimal', 'required' => false],
            'quantity'          => ['type' => 'integer', 'required' => false],
        ];
    }

    public static function relations( MapperInterface $mapper, EntityInterface $entity ) {

        return [
            'Product'    => $mapper->belongsTo( $entity, 'Entity\Product', 'product_id'),
        ];

    }
}
