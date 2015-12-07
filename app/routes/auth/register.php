<?php

use Katanium\Models\User;

/**
 * Render the register page
 *
 * Simply render
 */
$app->get('/register', function($req, $res, $args) {

	return $this->view->render($res, 'auth/signup.twig', ['signup' => true]);

})->setName('register')->add($app->getContainer()['guest']);


/**
 * Proccess register data
 */
$app->post('/register', function($req, $res, $args) use($app) {

	$params = $req->getParams();

	// Set validator
	$v = $this['validator'];
	$v->validate([
		'email'            => [ params['email'], 'required|email|uniqueEmail' ],
		'fullName'         => [ params['fullName'], 'required' ],
		'password'         => [ params['password'], 'required|min(6)' ],
		'password_confirm' => [ params['password_confirm'], 'required|matches(password)' ]
	]);

	if( $v->passes() ) {
		// Before anything else, generate a random string
		// with the purpose of activating the newly-registred user later
		$identifier = $this['randomlib']->generateString(128);

		// Create username
		// Take 'dewey992' out of dewey992@gmail.com
		$uname = explode('@', params['email']);
		$user  = new User;

		// then check if the username already exists
		if ( (bool) $user->where('displayName', $uname[0])->count() ) {
			// explode once more
			// Take the 'gmail' from gmail.com
			$emailhost = explode( '.', $uname[1] );
			// Concat the string
			// So that the result would be dewey992gmail
			$user->displayName = $uname[0] . $emailhost[0];
		}
		else {
			$user->displayName = $uname[0];
		}

		// Save to DB
		$user->email       = $post->email;
		$user->fullName    = $post->fullName;
		$user->password    = $this['hash']->password( $post->password );
		$user->active      = 0;
		$user->active_hash = $this['hash']->hash( $identifier );

		$user->save();

		// Send an email
		$data = array(
			'user'       => $user,
			'identifier' => $identifier
		);
		$this['mail']->send('mail/registration.twig', $data, function($message) use($user) {
			// define the email message
			$message->to( $user->email );
			//$message->subject('Terima kasih telah mendaftar di Katanium');
			$message->subject('Account Activation');
		});

		// Notify user
		$app->flash('msg', 'You have been registered');
		die( json_encode([ 'msg' => 'Berhasil yeaay!', 'error' => false ]) );
		// return $app->response->redirect( $app->urlFor('home') );
	}
	else {
		$errors = $v->errors()->all();

		// Output the message
		$res->withStatus(400)->withHeader('Content-Type', 'application/json');
		die( json_encode([ 'msg' => $errors, 'error' => true ]) );
	}

})->setName('register.post')->add($app->getContainer()['guest']);
