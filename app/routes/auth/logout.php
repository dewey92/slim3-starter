<?php

/* Logout */
$app->get('/logout', $registered(), function() use($app) {
	// Unset the session
	unset( $_SESSION[$app->config->get('auth.session')] );

	// Get and clear the cookie
	if ($app->getCookie($app->config->get('auth.remember')) ) {
		$app->auth->removeRememberCredentials();
		$app->deleteCookie($app->getCookie($app->config->get('auth.remember')));
	}

	// Finally redirect to home
	return $app->response->redirect( $app->request->getRootUri() . $app->request->getResourceUri() );
})->name('logout');
