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

class Address extends \Spot\Entity
{

    protected static $table     = 'addresses';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'user_id'      => ['type' => 'integer', 'index' => true, 'required' => true],
            'address'      => ['type' => 'string', 'required' => false],
            'county_id'    => ['type' => 'integer', 'index' => true],
            'postal_code'  => ['type' => 'string']
        ];
    }
}
