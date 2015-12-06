<?php

use Katanium\Models\User;

/* Render the register page */
$app->get('/login', $guest(), function() use($app) {

	$redirectUrl = isset($_GET['redirectUrl']) ? $_GET['redirectUrl'] : $app->urlFor('home');

	//render it!
	$app->view()->appendData(['login' => true, 'redirectUrl' => $redirectUrl]);
	$app->render('auth/login.twig');

})->name('login');

/* Login process */
$app->post('/login', $guest(), function() use($app) {

	$req = $app->req;

	// Set validator
	$v = $app->validator;
	$v->validate([
		'email'    => [ $req->email, 'required|email' ],
		'password' => [ $req->password, 'required' ]
	]);

	if (empty( $req->remember )) {
		$req->remember = 'off';
	}

	if( $v->passes() ) {
		// Search in DB
		$user = User::where('email', $req->email)->active()->first();

		// If email and password both exist and match in database
		if( $user && $app->hash->passwordCheck( $req->password, $user->password ) ) {
			// Set session for login
			$_SESSION[$app->config->get('auth.session')] = $user->user_id;

			if ($req->remember === 'on') {
				$rememberIdentifier = $app->randomlib->generateString(128);
				$rememberToken      = $app->randomlib->generateString(128);

				$user->updateRememberCredentials(
					$rememberIdentifier,
					$app->hash->hash($rememberToken)
				);

				// Set the cookie
				$app->setCookie(
					$app->config->get('auth.remember'),
					"{$rememberIdentifier}___{$rememberToken}",
					\Carbon\Carbon::parse('+1 week')->timestamp
				);
			}

			// Notify and rediect to where it should belong
			$redirectUrl = $req->redirectUrl !== $app->urlFor('login') ? $req->redirectUrl : $app->urlFor('home');

			return $app->response->redirect( $redirectUrl );
		}
		else {
			$app->render('auth/login.twig', [
				'error'   => true,
				'msg'     => ['Email dan password tidak cocok'],
				'request' => $req
			]);
		}
	}
	else {
		$app->render('auth/login.twig', [
			'error'   => true,
			'msg'     => $v->errors()->all(),
			'request' => $req
		]);
	}

})->name('login.post');
