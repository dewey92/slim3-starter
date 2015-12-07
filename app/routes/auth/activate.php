<?php

use Katanium\Models\User;

$app->get('/activate', function($req, $res, $args) {
	// Get the email and identifier
	$params = $req->getParams();

	$email      = $params['email'];
	$identifier = $params['identifier'];

	$hashedID   = $this->get('hash')->hash($identifier);
	$user       = User::where('email', $email)->where('active', 0)->first();

	if ( ! $user || ! $this->get('hash')->hashCheck($user->active_hash, $hashedID) ) {
		$app->flash('msg', 'Maaf, terdapat kesalahan dalam pengaktifan akun Anda di Katanium');
	}
	else {
		$user->activateAccount();

		$app->flash('msg', 'Akun Anda telah aktif. Anda bisa login sekarang');
		return $res->withRedirect($this->router->path_for('dashboard')); // Redirect to user-profile
	}

})->setName('activate')->add($app->getContainer()['guest']);