<?php
namespace Controllers;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


class User
{
	public function dashboard(Application $app, Request $request) 
	{

	}

	public function personalDetails(Application $app, Request $request) 
	{

	}

    public function register(Application $app, Request $request) {

        $form = $app['services.forms']->getUserRegisterForm($app, []);

        if($request->isMethod( Request::METHOD_POST )) {
            $form->handleRequest($request);


            if($form->isValid()) {

                $data = $form->getData();

                $mapper = $app['spot']->mapper('Entity\User');
                $user   = $mapper->build([
                    'email'         => $data['email'],
                    //'password'      => ,
                    'salt'          => base_convert(sha1(uniqid(mt_rand(), true)), 16, 36),
                    //'roles'         =>,
                    'name'          => $data['name'],
                    'time_created'  => new \DateTime(),
                    'username'      => $data['email'],
                    'isEnabled'     => true
                ]);

                $encoder = $app['security.encoder_factory']->getEncoder($user);
                $password = $encoder->encodePassword($data['password'], $user->getSalt());

                $user->set('password', $password);
                $mapper->save($user);

                $addressMapper = $app['spot']->mapper('Entity\Address');
                $address = $addressMapper->insert([
                    'user_id'   => $user->get('id'),
                    'county_id' => $data['county_id'],
                    'address' => $data['address'],
                    'postal_code' => $data['postal_code']
                ]);

                $app['session']->getFlashBag()->add('message', 'Contul a fost creat cu succes!');
                $backURL = $app['url_generator']->generate('user_register');
                return $app->redirect( $backURL );
            }
        }

        return $app['twig']->render('user/register.html', [
            'form' => $form->createView()
        ]);
    }

    function login(Application $app, Request $request) {
        $authException = $app['user.last_auth_exception']($request);
        return $app['twig']->render('user/login.html', array(
            'error' => $authException ? $authException->getMessageKey() : null,
            'last_username' => $app['session']->get('_security.last_username')
        ));
    }
}
