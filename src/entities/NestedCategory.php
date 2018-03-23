<?php
namespace Entity;

use Spot\Entity;
use Spot\Mapper;
use Spot\EventEmitter;

use Util\Util;

class NestedCategory extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\NestedCategory';
    protected static $table     = 'nested_categories';

    public static function fields()
    {
        return [
            'id'           => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'name'         => ['type' => 'string',  'required' => true],
            'lft'           => ['type' => 'integer', 'index' => true],
            'rgt'           => ['type' => 'integer', 'index' => true],
            'description'  => ['type' => 'text'],
            'slug'         => ['type' => 'string', 'index' => true],
            'active'       => ['type' => 'boolean', 'default' => false, 'required' => false],
        ];
    }

    public function getWidth() {
        return $this->rgt - $this->lft;
    }

    public function hasChildren() {
        return ($this->rgt - $this->lft - 1) / 2 > 0;
    }

    public static function events( EventEmitter $eventEmitter ) {

        $table = self::$table;
        $eventEmitter->on('beforeSave', function ( Entity $entity, Mapper $mapper ) use ( $table ){

            $slug = Util::slugify( empty( $entity->slug ) ? $entity->title : $entity->slug );
            $entity->slug = $mapper->getNextSlug($table, $slug, $entity->id);

        } );

    }
}
