<?php

use Katanium\Models\User;

/**
 * Get the login page
 *
 * Additionally, pass URL to the login page where it should redirect
 * after submitting the username & password for user convenience
 *
 * @param $_GET['redirectUrl'] the URL where the page should redirect
 */
$app->get('/login', function($req, $res, $args) {

	$httpGet     = $req->getQueryParams();
	$redirectUrl = count($httpGet) ? $httpGet['redirectUrl'] : $app->urlFor('home');

	$data = [
		'login'       => true,
		'redirectUrl' => $redirectUrl
	];

	return $this->view->render($res, 'auth/login.twig', $data);

})->setName('login')->add($app->getContainer()['guest']);

/**
 * Proccess login data
 */
$app->post('/login', function($req, $res, $args) use($app) {
	$params = $req->getParams();

	$v = $this['validator'];
	$v->validate([
		'email'    => [$params['email'], 'required|email'],
		'password' => [$params['password'], 'required']
	]);

	$params['remember'] = isset($params['remember']) ? $params['remember'] : 'off';

	if( $v->passes() ) {
		// Search in DB
		$user = User::where('email', $req->email)->active()->first();

		// If email and password both exist and match in database
		if( $user && $this['hash']->passwordCheck( $req->password, $user->password ) ) {
			// Set session for login
			$_SESSION[ $this['myConfig']->get('auth.session') ] = $user->user_id;

			if ($req['remember'] === 'on') {
				$rememberIdentifier = $app->randomlib->generateString(128);
				$rememberToken      = $app->randomlib->generateString(128);

				$user->updateRememberCredentials(
					$rememberIdentifier,
					$this['hash']->hash($rememberToken)
				);

				// Set the cookie
				$app->setCookie(
					$this['myConfig']->get('auth.remember'),
					"{$rememberIdentifier}___{$rememberToken}",
					\Carbon\Carbon::parse('+1 week')->timestamp
				);
			}

			// Notify and rediect to where it should belong
			$redirectUrl = $params['redirectUrl'] !== $this->router->pathFor('login') ? $params['redirectUrl'] :  $this->router->pathFor('home');

			return $res->withRedirect($redirectUrl);
		}
		else {
			return $this->view->render($res, 'auth/login.twig', [
				'error'   => true,
				'msg'     => ['Email dan password tidak cocok'],
				'request' => $req
			]);
		}
	}
	else {
		$this->view->render($res, 'auth/login.twig', [
			'error'   => true,
			'msg'     => $v->errors()->all(),
			'request' => $req
		]);
	}

})->setName('login.post')->add($app->getContainer()['guest']);
