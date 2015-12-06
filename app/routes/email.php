<?php

use Katanium\Models\Email;

$app->group('/email', function() use($app) {

	// Get the posts!
	$app->get('/', function() use($app) {
		echo Email::all()->toJson();
	});

	// Create the post!
	$app->post('/', function() use($app) {
		// Validate input as Email
		$email = $app->request->post('email');

		if (! filter_var( $email, FILTER_VALIDATE_EMAIL )) {
			echo json_encode([ 'msg' => 'Invalid email format: ' . $email ]);
			return false;
		}
		// Check if email exists
		if ( Email::where('email', $email)->count() ) {
			// Kalo ada hasilnya
			echo json_encode([ 'msg' => 'Email kamu sudah terdaftar kok :D']);
			return;
		}
		else { // Then insert to db
			$em = new Email;
			$em->email = $email;
			$em->save();
			
			echo json_encode([ 'msg' => 'Terimakasih sudah mendaftar di Katanium. Kami akan memberi tahu ketika Kataium resmi diluncurkan' ]);
		}
	});
});