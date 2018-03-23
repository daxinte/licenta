<?php

namespace Entity;
use 
    \Spot\Entity,
    \Spot\Mapper,
    \Spot\MapperInterface,
    \Spot\EntityInterface,
    \Spot\EventEmitter
;

class Order extends \Spot\Entity
{

    protected static $mapper  = 'Mapper\Order';
    protected static $table  = 'orders';

    public static function fields()
    {
        return [
            'id'            => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'user_id'       => ['type' => 'integer', 'index'    => true],
            'date'          => ['type' => 'datetime'],
        ];
    }

    public static function relations( MapperInterface $mapper, EntityInterface $entity ) {

        return [
            'Items'    => $mapper->hasMany( $entity, 'Entity\OrderItem', 'order_id'),
        ];

    }

    public function getItemsNo() {
        $items = $this->get('Items');

        $itemsNo = 0;
        foreach($items as $i) $itemsNo += $i->get('quantity');

        return $itemsNo;
    }

    public function getTotals() {
        $items = $this->get('Items');

        $totals = 0;
        foreach($items as $i) $totals += $i->get('quantity') *  $i->get('price');

        return $totals;
    }
}
