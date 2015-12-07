<?php

$authenticationCkeck = function ($required) use($app, $c) {
	return function ($req, $res, $next) use($app, $c, $required) {

		/**
		 * This middleware handles such conditions where
		 *
		 * 1. The user is not authenticated BUT the page requires auth
		 * 2. The user is authenticated BUT the page is for guest only
		 */
		if (( ! $app->auth && $required ) || ( $app->auth && ! $required )) {

			$where = $req->getPath();

			/**
			 * Redirect to homepage when the use requests login page
			 * from login page itself. This will prevent infinite loop redirect
			 */
			if ($req->getPath() === $c->router->pathFor('login')) {
				$where = $c->router->pathFor('home');
			}

			/**
			 * Redirect to login page with the URL the user is currently on.
			 * So when the user has done commiting the login proccess,
			 * the app will automatically open the passed URL to keep the user's position
			 * before login
			 */
			$redirectUrl = $c->router->pathFor('login') . '?redirectUrl=' . $where;

			/**
			 * Redirect to home if the requested path is logout
			 *
			 * So when blabla (not completed yet)
			 */
			if ( ($req->getPath() === $c->router->pathFor('logout')) || ($app->auth && ! $required) ) {
				$redirectUrl = $c->router->pathFor('home');
			}

			return $res->withRedirect($redirectUrl);
		}

		return $next($req, $res);
	};
};

$c = $app->getContainer();

$c['registered'] = function ($c) use($authenticationCkeck) {
	return $authenticationCkeck(true);
};

$c['guest'] = function ($c) use($authenticationCkeck) {
	return $authenticationCkeck(false);
};
