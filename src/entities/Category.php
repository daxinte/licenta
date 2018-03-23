<?php
namespace Entity;

use Spot\Entity;
use Spot\Mapper;
use Spot\EventEmitter;

use Util\Util;

class Category extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\Category';
    protected static $table     = 'categories';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'title'        => ['type' => 'string',  'required' => true],
            'parent_id'    => ['type' => 'integer', 'index' => true],
            'description'  => ['type' => 'text'],
            'order_no'     => ['type' => 'integer', 'default' => 0],
            'active'       => ['type' => 'boolean', 'default' => false, 'required' => false],
            'type'         => ['type' => 'string'],
            'slug'         => ['type' => 'string', 'index' => true],
        ];
    }

    public static function relations(\Spot\MapperInterface $mapper, \Spot\EntityInterface $entity)
    {
        return [
            'Parent'    => $mapper->hasOne($entity, 'Entity\Category', 'parent_id'),
            'Children'  => $mapper->hasMany($entity, 'Entity\Category', 'parent_id')
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
