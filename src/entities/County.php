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

class County extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\County';
    protected static $table     = 'counties';

    public static function fields()
    {
        return [
            'id'                => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name'              => ['type' => 'string', 'required' => true]
        ];
    }
}
