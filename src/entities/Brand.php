<?php
namespace Entity;

use Spot\Entity;
use Spot\Mapper;
use Spot\EventEmitter;

use Util\Util;

class Brand extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\Brand';
    protected static $table     = 'brands';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'title'        => ['type' => 'string',  'required' => true],
            'slug'         => ['type' => 'string', 'index' => true],
            'order_no'     => ['type' => 'integer', 'default' => 0],
            'active'       => ['type' => 'boolean', 'default' => false, 'required' => false],
        ];
    }

    public static function events( EventEmitter $eventEmitter ) {

        $table = self::$table;
        $eventEmitter->on('beforeSave', function ( Entity $entity, Mapper $mapper ) use ( $table ){

            $slug = Util::slugify( empty( $entity->slug ) ? $entity->title : $entity->slug );
            $entity->slug = $mapper->getNextSlug($table, $slug, $entity->id);

        } );

    }
}
