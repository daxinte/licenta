<?php
namespace Controllers\Admin;

use 
    Silex\Application,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Filesystem\Filesystem,
    Util\UploadHandler,
    Imagine\Image\Box,
    Imagine\Image\Point

    ;

class Images {

    public function upload(Request $request, Application $app, $product_id) {

        $mapper = $app['spot']->mapper('Entity\Image');

        $uploadPath = $app['config']['upload']['images_dir'];
        $uploadWWW  = $app['url_generator']->generate('images_www', [], true);
        $upload_handler = new UploadHandler([
            'upload_dir'    => $uploadPath,
            'upload_url'    => $uploadWWW,
            'script_url'    => $app['url_generator']->generate('images_uploader', ['product_id' => $product_id]),
            'image_versions'    => [
                    ['auto_orient' => 1 ],
                    'thumbnail'     => [
                        'max_width'     => 400, 
                        'max_height'    => 200
                        ]
            ],
            'print_response'    => false
        ]);

        $r = $upload_handler->get_response();
        $imagine = new \Imagine\Gd\Imagine();

        foreach( $r['files'] as $i ) {
            $pathInfo = pathinfo( $i->name );

            //crop the image
            $image = $imagine->open( $uploadPath.$i->name );
            $name = $pathInfo['filename'].".300.".$pathInfo['extension'];
            $size = $image->getSize();

            $coords = $size->getWidth() > $size->getHeight() ? [0, ($size->getWidth() - $size->getHeight()) / 2, $size->getHeight() ] : [($size->getHeight() - $size->getWidth()) / 2 , 0, $size->getWidth()];
            $point  = new Point($coords[1], $coords[0]);

            $image
                ->crop($point, new Box( $coords[2], $coords[2] ))
                ->save($uploadPath.$name)
                ;

            $i->size300 = $name;
            unset( $image );

            //resize the box as we need
            $image = $imagine->open( $uploadPath.$i->name );

            foreach( $app['config']['images']['sizes'] as $k => $val ) {
                $name = $pathInfo['filename'].".$val.".$pathInfo['extension'];
                $image
                    ->resize( $image->getSize()->widen( $val ) )
                    ->save( $uploadPath.$name )
                    ;


                $i->$k = $name;
            }

            //save to db the name of the file
            $entity = $mapper->create([
                'name'      => $i->name,
                'product_id'  => $product_id,
            ]);

            $i->www = $uploadWWW;
            $i->id  = $entity->id;

        }

        return $app->json( $r );

    }

    public function remove(Request $request, Application $app, $id) {

        $fs     = new Filesystem();
        $mapper = $app['spot']->mapper('Entity\Image');
        $entity = $mapper->get( $id );

        if( false === $entity ) return new Response('The entity was not found', 404);

        $imgsPath = $app['config']['upload']['images_dir'];

        //remove phisical files
        foreach( $app['config']['images']['sizes'] as $k => $val ) {

            $fileName = $entity->getResizedName( $val );

            if( $fs->exists( $imgsPath.$fileName ) ) {
                $fs->remove( $imgsPath.$fileName );
            }
        }

        //crop, normal and thumb image
        $otherSources = [ $entity->getResizedName( 300 ), $entity->name, 'thumbnail/'.$entity->name ];
        foreach( $otherSources as $source ) {
            if( $fs->exists( $imgsPath.$source ) ) {
                $fs->remove( $imgsPath.$source );
            }
        }

        $mapper->delete( $entity );

        return "ok";

    }

}
