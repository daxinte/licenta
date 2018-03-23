<?php
namespace Controllers;

use Silex\Application
    ,Silex\ControllerProviderInterface
    ,Symfony\Component\HttpFoundation\Response
    ,Symfony\Component\HttpFoundation\Request
    ;

class Products 
{

    public function index( Application $app, Request $request ) {

        //afisare lista produse
        $productsMapper = $app['spot']->mapper('Entity\Product');
        $products = $productsMapper->all()->with('Images');

        $cart = $app['session']->get('cart');

        return $app['twig']->render('products/index.html', [
            'products'  => $products,
            'pageTitle' => 'Produse'
        ]);
    }

    public function details( Application $app, Request $request ) {

        //preiau parametrul din request
        $slug = $request->get('slug');

        //caut produsul in baza de date
        $mapper = $app['spot']->mapper('Entity\Product');
        $product = $mapper->getBySlug($slug);

        //daca produsul nu exista in baza de date, aceasta actiune returneaza un mesaj cu status 404
        if(false === $product) return new Response("Acest produs nu exista sau a fost dezactivat.", 404);

        //returnez template-ul de detalii prods
        $images = $product->get('Images');
        return $app['twig']->render('products/details.html', [
            'product'   => $product,
            'pageTitle' => $product->get('title'),
            'firstImage'=> $images->offsetExists(0) ? $images->first()->get('name') : false,
            'imagesWWW' => $app['url_generator']->generate('images_www'),
            'noImage'   => $app['config']['images']['default_store1']
        ]);
    }

}
