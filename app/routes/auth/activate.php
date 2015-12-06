<?php

use Katanium\Models\User;

$app->get('/activate', $guest(), function() use($app) {
	// Get the email and identifier
	$request    = $app->request;
	
	$email      = $request->get('email');
	$identifier = $request->get('identifier');
	
	$hashedID   = $app->hash->hash($identifier);
	$user       = User::where('email', $email)->where('active', 0)->first();

	if ( ! $user || ! $app->hash->hashCheck($user->active_hash, $hashedID) ) {
		$app->flash('msg', 'Maaf, terdapat kesalahan dalam pengaktifan akun Anda di Katanium');
	}
	else {
		$user->activateAccount();

		$app->flash('msg', 'Akun Anda telah aktif. Anda bisa login sekarang');
		return $app->redirect($app->urlFor('dashboard')); // Redirect to user-profile
	}

})->name('activate');