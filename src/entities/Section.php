<?php
namespace Entity;

use Spot\Entity;
use Spot\Mapper;
use Spot\EventEmitter;

use Util\Util;

class Section extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\Section';
    protected static $table     = 'sections';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'title'        => ['type' => 'string', 'required' => true],
            'slug'         => ['type' => 'string', 'required' => true],
            'description'  => ['type' => 'text'],
            'order_no'     => ['type' => 'integer', 'default' => 0, 'required' => true],
            'active'       => ['type' => 'boolean', 'required' => false],
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
