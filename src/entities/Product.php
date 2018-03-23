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

class Product extends \Spot\Entity
{

    protected static $mapper    = 'Mapper\Product';
    protected static $table     = 'products';

    private $imgPath;

    public static function fields()
    {
        return [
            'id'                => ['type' => 'integer', 'primary' => true, 'autoincrement' => true],
            'title'             => ['type' => 'string', 'required' => true],
            'code'              => ['type' => 'string', 'required' => false],
            'price'             => ['type' => 'decimal', 'required' => false],
            'category_id'       => ['type' => 'integer', 'index' => true, 'required' => false],
            'brand_id'          => ['type' => 'integer', 'index' => true, 'required' => false],
            'slug'              => ['type' => 'string', 'required' => true],
            'description'       => ['type' => 'text', 'required' => false],
            'stock'             => ['type' => 'integer', 'required' => false],
            'active'            => ['type' => 'boolean', 'required' => false],
            'tags'              => ['type' => 'string', 'required' => false],
            'meta_title'        => ['type' => 'string', 'required' => false],
            'meta_keywords'     => ['type' => 'text', 'required' => false],
            'meta_description'  => ['type' => 'text', 'required' => false],
            'created_at'        => ['type' => 'datetime'],
            'updated_at'        => ['type' => 'datetime'],
        ];
    }

    public static function relations( MapperInterface $mapper, EntityInterface $entity ) {

        return [
            'Images'    => $mapper->hasMany( $entity, 'Entity\Image', 'product_id'),
        ];

    }

    public static function events( EventEmitter $eventEmitter ) {

        $table = self::$table;
        $eventEmitter->on('beforeSave', function ( Entity $entity, Mapper $mapper ) use ( $table ){

            $slug = Util::slugify( empty( $entity->slug ) ? $entity->title : $entity->slug );
            $entity->slug = $mapper->getNextSlug($table, $slug, $entity->id);

            if( $entity->isNew() ) {
                $entity->created_at = new \DateTime();
            }

            $entity->updated_at = new \DateTime();

        } );

    }

    public function setImgPath($imgPath = null) {
        if (empty($imgPath)) return;
        $this->imgPath = $imgPath;
    }

    public function getImgPath() {
        return $this->imgPath;
    }
}
