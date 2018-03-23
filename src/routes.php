<?php

use
     Silex\Application
    ,Symfony\Component\HttpFoundation\Request
    ,Symfony\Component\HttpFoundation\Response
;

//$app->get('/login', function(Request $request) use ($app) {
//    return $app['twig']->render('login.html', array(
//        'error'         => $app['security.last_error']($request),
//        'last_username' => $app['session']->get('_security.last_username'),
//    ));
//});


//front routes
//$app->get('/', 'controllers.store:index')->bind('homepage');
$app->get('/', 'controllers.products:index')->bind('products');
$app->get('/produse/{slug}.html', 'controllers.products:details')->bind('products_details');
$app->get('/cart', 'controllers.orders:cart')->bind('cart');
$app->get('/cart/reset', 'controllers.orders:reset')->bind('cart_reset');
$app->match('/cart/add/product/{slug}', 'controllers.orders:addToCart')->bind('add_to_cart');
$app->get('/checkout', 'controllers.orders:checkout')->bind('checkout');
$app->get('/thankyou.html', 'controllers.orders:success')->bind('thankyou');

$app->get('/comenzi', 'controllers.orders:index')->bind('orders');
$app->get('/comenzi/{id}', 'controllers.orders:details')->bind('order_details');


$app->match('/user/login', 'controllers.users:login')->bind('login_form');
$app->match('/user/register', 'controllers.users:register')->bind('user_register');
//$app->match('/user/logout', 'controllers.users:register')->bind('user_register');




//admin routes
$app->get('/admin/', 'admin_controllers.main:dashboard')->bind('admin_dashboard');
$app->get('/admin/products/', 'admin_controllers.products:index')->bind('admin_products');
$app->match('/admin/products/create/', 'admin_controllers.products:create')->bind('admin_products_create');
$app->match('/admin/products/{id}/edit', 'admin_controllers.products:edit')->bind('admin_products_edit');
$app->get('/admin/products/{id}/delete', 'admin_controllers.products:delete')->bind('admin_products_delete');
$app->get('/admin/products/{id}/images', 'admin_controllers.products:images')->bind('admin_products_images');

//sections
$app->get('/admin/sections/', 'admin_controllers.sections:index')->bind('admin_sections');
$app->match('/admin/sections/create/', 'admin_controllers.sections:create')->bind('admin_sections_create');
$app->match('/admin/sections/{id}/edit', 'admin_controllers.sections:edit')->bind('admin_sections_edit');
$app->get('/admin/sections/{id}/delete', 'admin_controllers.sections:delete')->bind('admin_sections_delete');

//categories
$app->get('/admin/categories/', 'admin_controllers.categories:tree')->bind('admin_categories');
$app->match('/admin/categories/create/', 'admin_controllers.categories:create2')->bind('admin_categories_create');
$app->match('/admin/categories/{id}/edit', 'admin_controllers.categories:edit2')->bind('admin_categories_edit');
$app->get('/admin/categories/{id}/delete', 'admin_controllers.categories:delete2')->bind('admin_categories_delete');
$app->get('/admin/categories/{id}/move-left', 'admin_controllers.categories:moveLeft')->bind('admin_categories_move_left');
$app->get('/admin/categories/{id}/move-right', 'admin_controllers.categories:moveRight')->bind('admin_categories_move_right');

//brands
$app->get('/admin/brands/', 'admin_controllers.brands:index')->bind('admin_brands');
$app->match('/admin/brands/create/', 'admin_controllers.brands:create')->bind('admin_brands_create');
$app->match('/admin/brands/{id}/edit', 'admin_controllers.brands:edit')->bind('admin_brands_edit');
$app->get('/admin/brands/{id}/delete', 'admin_controllers.brands:delete')->bind('admin_brands_delete');

$app->match('/admin/images/upload/{product_id}', 'admin_controllers.images:upload')->bind('images_uploader');
$app->get('/admin/images/remove/{id}', 'admin_controllers.images:remove')->bind('images_remove');
$app->get('/media/images/')->bind('images_www');
