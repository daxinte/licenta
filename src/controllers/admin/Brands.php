<?php
namespace Controllers\Admin;

use Silex\Application
    ,Silex\ControllerProviderInterface
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\Request
    ;

class Brands
{

    public function index(Request $request, Application $app) {

        if($app['request']->isXMLHttpRequest()) {
            //handle ajax action for pages table
            $mapper = $app['spot']->mapper('Entity\Brand');
            $data = $mapper->getForDataTable($request);

            foreach($data['results'] as $pos => $arr) {

                $data['results'][$pos][2] = empty($arr[2]) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
                $data['results'][$pos][3] = '<a class="btn btn-primary btn-xs" href="'.$app['url_generator']->generate('admin_brands_edit', array('id' => $arr[3])).'">Detalii</a>';

            }

            return $app->json([
                        'sEcho'             => $app['request']->get('sEcho')
                        ,'iTotalRecords'    => $data['totalRowsFound']
                        ,'iTotalDisplayRecords' => $data['totalRowsFound']
                        ,'aaData'           => $data['results']
            ]);
        }

        $gridO = $app['services.datatables']->getBrandsGrid(
            $app['url_generator'],
            array('pageTitle'   => 'Branduri / Producatori')
        );

        return $app['twig']->render('admin/brands/list.html', array(
            'gridO' => $gridO
        ));
    }

    public function create(Request $request, Application $app) {

        $entity    = array();
        $form = $app['services.forms']->getBrandForm($app, $entity);

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Entitatea a fost salvata cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_brands_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/brands/edit.html', array(
            'object'        => $entity
            ,'form'         => $form->createView()
            ,'formAction'   => $app['url_generator']->generate('admin_brands_create')
            ,'currPageTitle'=> "Adaugare brand"
        ));

    }

    public function edit( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Brand');
        $entity= $mapper->get($id);

        if( false === $entity ) return new Response("Entitatea cautata nu exista", 404);

        $form = $app['services.forms']->getBrandForm($app, $entity->toArray());

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Entitatea a fost salvata cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_brands_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/brands/edit.html', array(
            'object'        => $entity
            ,'form'         => $form->createView()
            ,'formAction'   => ''
            ,'currPageTitle'=> "Editare brand \"".$entity->title."\""
        ));

    }

    private function processForm( $form , $app, $request) {

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data   = $form->getData();

            $mapper = $app['spot']->mapper('Entity\Brand');

            if( !empty( $data['id'] ) ) {
                $entity = $mapper->get( $data['id'] );
                $entity->data( $data );
            } else {
                $entity= $mapper->build($data);
            }

            $mapper->save( $entity ); 

            $id = $entity->id;

            return $id;
        }

        return null;

    }

    public function delete ( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Brand');
        $entity= $mapper->get($id);

        if( false === $entity OR is_null( $entity ) ) return new Response("Nu am gasit entitatea", 404);

        $mapper->delete($entity);
        $app['session']->getFlashBag()->add('message', 'Entitatea a fost stearsa cu succes');
        return $app->redirect( $app['url_generator']->generate('admin_brands') );

    }
}
