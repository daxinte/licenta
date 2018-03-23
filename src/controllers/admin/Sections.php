<?php
namespace Controllers\Admin;

use Silex\Application
    ,Silex\ControllerProviderInterface
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\Request
    ;

class Sections
{

    public function index(Request $request, Application $app) {

        if($app['request']->isXMLHttpRequest()) {
            //handle ajax action for pages table
            $productsMapper = $app['spot']->mapper('Entity\Category');
            $data = $productsMapper->getSectionsForDataTable($request);

            foreach($data['results'] as $pos => $arr) {

                $data['results'][$pos][3] = empty($arr[3]) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
                $data['results'][$pos][4] = '<a class="btn btn-primary btn-xs" href="'.$app['url_generator']->generate('admin_sections_edit', array('id' => $arr[4])).'">Detalii</a>';

            }

            return $app->json([
                        'sEcho'             => $app['request']->get('sEcho')
                        ,'iTotalRecords'    => $data['totalRowsFound']
                        ,'iTotalDisplayRecords' => $data['totalRowsFound']
                        ,'aaData'           => $data['results']
            ]);
        }

        $gridO = $app['services.datatables']->getSectionsGrid(
            $app['url_generator'],
            array('pageTitle'   => 'Sectiuni')
        );

        return $app['twig']->render('admin/sections/list.html', array(
            'gridO' => $gridO
        ));
    }

    public function create(Request $request, Application $app) {

        $section    = array();
        $form = $app['services.forms']->getSectionForm($app, $section);

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Sectiunea a fost salvata cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_sections_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/sections/edit.html', array(
            'object'        => $section
            ,'form'         => $form->createView()
            ,'formAction'   => $app['url_generator']->generate('admin_sections_create')
            ,'currPageTitle'=> "Adaugare sectiune"
            ,'currPageTitle'=> "Adaugare sectiune"
        ));

    }

    public function edit( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Category');
        $entity= $mapper->get($id);

        if( false === $entity ) return new Response("Entitatea cautats nu exista", 404);

        $form = $app['services.forms']->getSectionForm($app, $entity->toArray());

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Entitatea a fost salvata cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_sections_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/sections/edit.html', array(
            'object'        => $entity
            ,'form'         => $form->createView()
            ,'formAction'   => ''
            ,'currPageTitle'=> "Adaugare sectiune"
        ));

    }

    private function processForm( $form , $app, $request) {

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data   = $form->getData();

            $mapper = $app['spot']->mapper('Entity\Category');

            if( !empty( $data['id'] ) ) {
                $entity = $mapper->get( $data['id'] );
                $entity->data( $data );
            } else {
                $data['type'] = 'section';
                $entity= $mapper->build($data);
            }

            $mapper->save( $entity ); 

            $id = $entity->id;

            return $id;
        }

        return null;

    }

    public function delete ( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Category');
        $section= $mapper->get($id);

        if( false === $section OR is_null( $section ) ) return new Response("Nu am gasit sectiunea", 404);

        $mapper->delete($section);
        $app['session']->getFlashBag()->add('message', 'Sectiunea a fost stearsa cu succes');
        return $app->redirect( $app['url_generator']->generate('admin_sections') );

    }
}
