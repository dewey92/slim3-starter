<?php

use Katanium\Models\User;

/* Render the register page */
$app->get('/register', $guest(), function() use($app) {
	//render it!
	$app->view()->appendData(['signup' => true]);
	$app->render('auth/signup.twig');

})->name('register');

/* Create the user */
$app->post('/register', $guest(), function() use($app) {

	$post = $app->req;

	// Set validator
	$v = $app->validator;
	$v->validate([
		'email'            => [ $post->email, 'required|email|uniqueEmail' ],
		'fullName'         => [ $post->fullName, 'required' ],
		'password'         => [ $post->password, 'required|min(6)' ],
		'password_confirm' => [ $post->password_confirm, 'required|matches(password)' ]
	]);

	if( $v->passes() ) {
		// Before anything else, generate a random string
		// with the purpose of activating the newly-registred user later
		$identifier = $app->randomlib->generateString(128);

		// Create username
		// Take 'dewey992' out of dewey992@gmail.com
		$uname = explode('@', $post->email);
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
		$user->password    = $app->hash->password( $post->password );
		$user->active      = 0;
		$user->active_hash = $app->hash->hash( $identifier );

		$user->save();

		// Send an email
		$data = array(
			'user'       => $user,
			'identifier' => $identifier
		);
		$app->mail->send('mail/registration.twig', $data, function($message) use($user) {
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
		$app->response->setStatus(400);
		$app->response->headers->set('Content-Type', 'application/json');
		die( json_encode([ 'msg' => $errors, 'error' => true ]) );
	}

})->name('register.post');
