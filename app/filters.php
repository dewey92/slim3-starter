<?php

$authenticationCkeck = function($required) use($app) {
	return function($req, $res, $next) use($app, $required) {
		// Check permission
		if (( ! $app->auth && $required ) || ( $app->auth && ! $required )) {

			$where = $req->getPath();

			if ($req->getPath() === $app->router->pathFor('login')) {
				$where = $app->router->pathFor('home');
			}

			$redirectUrl = $app->router->pathFor('login') . '?redirectUrl=' . $where;

			if ( ($req->getPath() === $app->urlFor('logout')) || ($app->auth && ! $required) ) {
				$redirectUrl = $app->router->pathFor('home');
			}

			return $res->withHeader('Location', $redirectUrl);
		}

		return $next($req, $res);
	};
};

$c = $app->getContainer();

$c['registered'] = function ($c) {
	return $authenticationCkeck(true);
};

$c['guest'] = function ($c) {
	return $authenticationCkeck(false);
};
