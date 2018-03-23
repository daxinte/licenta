<?php
use
     Silex\Provider\TwigServiceProvider
    ,Silex\Provider\FormServiceProvider
    ,Silex\Provider\ValidatorServiceProvider
    ,Silex\Provider\ServiceControllerServiceProvider
    ,Symfony\Component\Translation\Loader\YamlFileLoader

    ,Provider\ImagineServiceProvider

    ,Services\DataTables
    ,Services\Forms

    ,Controllers\Products as ProductsController
    ,Controllers\Store as StoreController
    ,Controllers\Orders as OrdersController
    ,Controllers\User as UserController


    ,Controllers\Admin\Main as AdminMainController
    ,Controllers\Admin\Products as AdminProductsController
    ,Controllers\Admin\Sections as AdminSectionsController
    ,Controllers\Admin\Categories as AdminCategoriesController
    ,Controllers\Admin\Brands as AdminBrandsController
    ,Controllers\Admin\Images as AdminImagesController
    ;

$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/../config/settings.yml'));
$app->register(new TwigServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('ro'),
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => getenv('CMS_DB_NAME') ?: 'lstore',
        'user'      => getenv('CMS_DB_USER') ?: 'root',
        'password'  => getenv('CMS_DB_PASS'), 
        'charset'   => 'utf8',
    ),
));

$simpleUserProvider = new SimpleUser\UserServiceProvider();
$app->register($simpleUserProvider);

//$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
//
//    $arrLangs = ['ro'];
//
//    $translator->addLoader('yaml', new YamlFileLoader());
//
//    foreach($arrLangs as $l) {
//        $translator->addResource('yaml', __DIR__.'/../config/locale/'.$l.'.yml', $l);
//    }
//
//    return $translator;
//}));


//$userServiceProvider = new SimpleUser\UserServiceProvider();
//$app->register($userServiceProvider);
// Register the SimpleUser service provider.
//$simpleUserProvider = new SimpleUser\UserServiceProvider();
//$app->register($simpleUserProvider);

//$app->register(new Silex\Provider\SecurityServiceProvider(), array(
//    'security.firewalls' => array(
//        'unsecured' => [
//            'anonymous' => true
//        ],
//        'admin' => array(
//            'pattern' => '^/admin/',
//            'form' => array('login_path'    => '/login', 'check_path' => '/admin/login_check'),
//            'logout' => array('logout_path' => '/admin/logout'),
//            'users' => array(
//                //password is petstore
//                'admin' => array('ROLE_ADMIN', 'JqG5NvNtP8cuFNeWxQqc4Dg+c8SIKHkOxxh6+LMAqFunBy3OntXh2O5kanMfCMRZB9G3r8DW7mtPVI8qfsiF+g=='),
//            ),
//        )
//    )
//));


$app['cms.settings'] = $app->share(function() use($app) {
    return array(
         'APP_NAME'         => 'Webstore'
        ,'CMS_OWNER_NAME'   => 'Webstore'
        ,'languages'        => ['ro', 'en', 'ru', 'de']
    );
});

$app['twig'] = $app->extend('twig', function ($twig, $app) {

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        return $app['request_stack']->getMasterRequest()->getBasepath().'/'.$asset;
    }));
    return $twig;
});

$app->mount('/user', $simpleUserProvider);

$app['twig.path'] = array(__DIR__.'/../src/views');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

//controllers
$app['controllers.products'] = $app->share(function() use($app) {
    return new ProductsController();
});
$app['controllers.store'] = $app->share(function() use($app) {
    return new StoreController();
});
$app['controllers.orders'] = $app->share(function() use($app) {
    return new OrdersController();
});
$app['controllers.users'] = $app->share(function() use($app) {
    return new UserController();
});

//admin controllers
$app['admin_controllers.main'] = $app->share(function() use($app) {
    return new AdminMainController();
});
$app['admin_controllers.products'] = $app->share(function() use($app) {
    return new AdminProductsController();
});
$app['admin_controllers.categories'] = $app->share(function() use($app) {
    return new AdminCategoriesController();
});
$app['admin_controllers.sections'] = $app->share(function() use($app) {
    return new AdminSectionsController();
});
$app['admin_controllers.brands'] = $app->share(function() use($app) {
    return new AdminBrandsController();
});

$app['admin_controllers.images'] = $app->share(function() use ($app) {
    return new AdminImagesController();
});

//services
$app['services.datatables'] = $app->share(function() use($app) {
    return new Services\DataTables();
});
$app['services.forms'] = $app->share(function() use($app) {
    return new Services\Forms();
});
$app['services.util'] = $app->share(function() use($app) {
    return new Services\Util();
});

$app->register(
    new Provider\SpotServiceProvider($app['config']['database'])
);
$app->register(new ImagineServiceProvider());

//user config
$app['user.options'] = array(

    // Specify custom view templates here.
    'templates' => array(
        'layout'    => 'layout.html',
        'register'  => 'user/register.html',
//        'register-confirmation-sent' => '@user/register-confirmation-sent.twig',
        'login' => 'user/login.html',
//        'login-confirmation-needed' => '@user/login-confirmation-needed.twig',
//        'forgot-password' => '@user/forgot-password.twig',
//        'reset-password' => '@user/reset-password.twig',
          'view' => 'user/welcome.html',
//        'edit' => 'admin/users/edit.html',
//        'list' => 'admin/users/list.html',
    )
);

$app['security.firewalls'] = array(
    'login' => array(
        'pattern' => '^/user/login$',
        'anonymous' => true,
    ),
    'secured_area' => array(

        'pattern' => '^.*$',
        'anonymous' => true,
        'form' => array(
            'login_path' => '/user/login',
            'check_path' => '/user/login_check',
        ),
        'logout' => array(
            'logout_path' => '/user/logout',
        ),
        'users' => $app->share(function($app) { return $app['user.manager']; }),
    ),
);

$app['security.access_rules'] = array(
    array('^/admin', 'ROLE_ADMIN')
);

//$userServiceProvider = new SimpleUser\UserServiceProvider();
//$app->register($userServiceProvider);
//$app->mount('/users', $simpleUserProvider);

//verify the existence of a cart in session
if(!$app['session']->has('cart')) {
    $app['session']->set('cart', new Util\Cart());
}
