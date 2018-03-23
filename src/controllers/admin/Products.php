<?php
namespace Controllers\Admin;

use Silex\Application
    ,Silex\ControllerProviderInterface
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\Request
    ;

class Products
{

    public function index(Request $request, Application $app) {


        if($app['request']->isXMLHttpRequest()) {
            //handle ajax action for pages table
            $productsMapper = $app['spot']->mapper('Entity\Product');
            $data = $productsMapper->getForDataTable($request);

            foreach($data['results'] as $pos => $arr) {

                $data['results'][$pos][2] = empty($arr[2]) ? '<span class="label label-default">Nu</span>' : '<span class="label label-primary">Da</span>';
                $data['results'][$pos][5] = '<a class="btn btn-primary btn-xs" href="'.$app['url_generator']->generate('admin_products_edit', array('id' => $arr[5])).'">Detalii</a>';

            }

            return $app->json([
                        'sEcho'             => $app['request']->get('sEcho')
                        ,'iTotalRecords'    => $data['totalRowsFound']
                        ,'iTotalDisplayRecords' => $data['totalRowsFound']
                        ,'aaData'           => $data['results']
            ]);
        }

        $gridO = $app['services.datatables']->getProductsGrid(
            $app['url_generator'],
            array('pageTitle'   => 'Produse')
        );

        return $app['twig']->render('admin/products/index.html', array(
            'gridO' => $gridO
        ));

    }

    public function create(Request $request, Application $app) {

        $product    = array();

        $form = $app['services.forms']->getProductForm($app, $product);

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Produsul a fost salvat cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_products_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/products/edit.html', array(
            'object'        => $product
            ,'form'         => $form->createView()
            ,'formAction'   => $app['url_generator']->generate('admin_products_create')
            ,'currPageTitle'=> "Adaugare produs"
        ));

    }

    public function edit( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Product');
        $product= $mapper->get($id);

        if( false === $product ) return new Response("Produsul cautat nu exista", 404);

        $form = $app['services.forms']->getProductForm($app, $product->toArray());

        //process the form
        $id = $this->processForm( $form, $app, $request);
        if( !is_null($id) ) {

            $app['session']->getFlashBag()->add('message', 'Produsul a fost salvat cu succes');
            return $app->redirect( $app['url_generator']->generate('admin_products_edit', array('id' => $id)) );

        }

        return $app['twig']->render('admin/products/edit.html', array(
            'object'        => $product
            ,'form'         => $form->createView()
            ,'formAction'   => ''
            ,'currPageTitle'=> "Adaugare produs"
        ));

    }

    public function delete ( Request $request, Application $app, $id ) {

        $mapper = $app['spot']->mapper('Entity\Product');
        $product= $mapper->get($id);

        if( false === $product OR is_null( $product ) ) return new Response("Nu am gasit produsul", 404);

        $mapper->delete($product);
        $app['session']->getFlashBag()->add('message', 'Produsul a fost sters cu succes');
        return $app->redirect( $app['url_generator']->generate('admin_products') );

    }

    private function processForm( $form , $app, $request) {

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data   = $form->getData();
            $mapper = $app['spot']->mapper('Entity\Product');

            if( !empty( $data['id'] ) ) {
                $product = $mapper->get( $data['id'] );
                $product->data( $data );
            } else {
                $product= $mapper->build($data);
            }

            $mapper->save( $product ); 

            $id = $product->id;

            return $id;
        }

        return null;

    }

    //images
    public function images(Request $request, Application $app, $id) {

        if( empty($id) ) return new Response( 'Entity not found', 404);

        $mapper = $app['spot']->mapper( 'Entity\Product' );
        $entity = $mapper->where(['id' => $id])->with('Images')->first();

        return $app['twig']->render('admin/products/images.html', [
            'entity'    => $entity
        ]);
    }

}
