<?php
namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Orders 
{

	### METODE PENTRU CART ###

	/* vizualizare cart */
	public function cart(Application $app, Request $request) 
	{

        $cart = $app['session']->get('cart');
        $items = $cart->getItems();

        return $app['twig']->render('orders/cart.html', [
            'cart'      => $cart,
            'pageTitle' => 'Cosul de cumparaturi'
        ]);
	}

	public function checkout(Application $app, Request $request) 
	{

        $token = $app['security']->getToken(); 
        if (null !== $token) {
            $user = $token->getUser();
        } else {
            return new Response("Nu aveti acces la aceasta pagina", 401);
        }

        if(!$user->hasRole('ROLE_USER'))
            return new Response("Nu aveti acces la aceasta pagina", 401);




        $cart = $app['session']->get('cart');

        if($cart->getItemsNo() < 1) {
            return "Nu exista produse in cos";
        }



        //cream o noua comanda si salvam item-urile in baza de date
        $ordersMapper = $app['spot']->mapper('Entity\Order');
        $order = $ordersMapper->create([
            'user_id'   => $user->getId(),
            'date'      => new \DateTime()
        ]);


        $orderItemMapper = $app['spot']->mapper('Entity\OrderItem');
        foreach( $cart->getItems() as $cartItem ) {
            $orderItem = $orderItemMapper->create([
                'order_id'      => $order->get('id'),
                'product_id'    => $cartItem['item']->get('id'),
                'price'         => $cartItem['item']->get('price'),
                'quantity'      => $cartItem['quantity']
            ]);
        }

        $cart->reset();

        $app['session']->getFlashBag()->add('message', 'Comanda dumneavoastra a fost trimisa cu succes!');
        $backURL = $app['url_generator']->generate('thankyou');
        return $app->redirect( $backURL );
	}

	public function success(Application $app, Request $request) 
	{
        return $app['twig']->render('orders/thankyou.html', [
            'pageTitle' => 'Va multumim ca ati cumparat de la noi.'
        ]);
	}

	##interactiuni cu cart-ul
	public function addToCart(Application $app, Request $request)
	{
        //preiau parametrul din request
        $slug = $request->get('slug');

        //caut produsul in baza de date
        $mapper = $app['spot']->mapper('Entity\Product');
        $product = $mapper->getBySlug($slug);

        $imgs = $product->get('Images');
        if($imgs->count()) {
            $img = $imgs->first();
            $product->setImgPath($app['config']['upload']['images_www'].$img->get('name'));
        } else {
            $product->setImgPath($app['config']['images']['default_store1']);
        }

        $quantity = $request->get('quantity', 1);

        //daca produsul nu exista in baza de date, aceasta actiune returneaza un mesaj cu status 404
        if(false === $product) return new Response("Acest produs nu exista sau a fost dezactivat.", 404);

        //daca totul e in regula, introduc produsul in cos, si updatez cosul in sesiune
        $cart = $app['session']
        			->get('cart')
        			->addItem($product, $quantity)
        			;
        //$app['session']->set('cart', $cart);

        //redirectez utilizatorul, cu un mesaj
        $app['session']->getFlashBag()->add('message', 'Produsul a fost adaugat in cos!');
        $backURL = $request->isMethod('post') ? $app['url_generator']->generate('products_details', ['slug' => $product->get('slug')]) : $app['url_generator']->generate('products');
        return $app->redirect( $backURL );

    }

    public function reset(Application $app, Request $request)
    {
        $cart = $app['session']->get('cart')->reset();
        //->reset();
        return new Response('Ok');
    }

	public function removeFromCart(Application $app, Request $request)
	{
		
	}


	##METODE PENTRU AUTHENTICATED USER###

	/*
		lista de comenzi - authenticated user ( user account )
	*/
	public function index(Application $app, Request $request) 
	{
        $token = $app['security']->getToken(); 
        if (null !== $token) {
            $user = $token->getUser();
        } else {
            return new Response("Nu aveti acces la aceasta pagina", 401);
        }

        if(!$user->hasRole('ROLE_USER'))
            return new Response("Nu aveti acces la aceasta pagina", 401);

        $mapper = $app['spot']->mapper('Entity\Order');
        $orders = $mapper->all()->with('Items')->execute();

        return $app['twig']->render('orders/index.html', [
            'orders'    => $orders,
            'pageTitle' => 'Comenzile mele'
        ]);
	}

	public function details(Application $app, Request $request, $id) 
	{
        $token = $app['security']->getToken(); 
        if (null !== $token) {
            $user = $token->getUser();
        } else {
            return new Response("Nu aveti acces la aceasta pagina", 401);
        }

        if(!$user->hasRole('ROLE_USER'))
            return new Response("Nu aveti acces la aceasta pagina", 401);

        $mapper = $app['spot']->mapper('Entity\Order');
        $items = $mapper->getFullWithImages($id);

        return $app['twig']->render('orders/details.html', [
            'items'    => $items,
            'pageTitle' => 'Detalii comanda #'.$id
        ]);
	}
}
