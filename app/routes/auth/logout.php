<?php

/* Logout */
$app->get('/logout', function($req, $res, $next) {

	unset( $_SESSION[ $this['myConfig']->get('auth.session')] ); // Unset the session

	// Get and clear the cookie
	if ($app->getCookie($app->config->get('auth.remember')) ) {
		$app->auth->removeRememberCredentials();
		$app->deleteCookie($app->getCookie($app->config->get('auth.remember')));
	}

	return $res->withRedirect( $req->getRootUri() . $req->getResourceUri() );

})->setName('logout')->add($app->getContainer()['registered']);
