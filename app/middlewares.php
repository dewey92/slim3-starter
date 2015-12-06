<?php

use Katanium\Helpers\DatabaseAdapter;

$app->add($app->getContainer()->get('csrf'));

$app->add(function ($req, $res, $next) {
	if (null == DatabaseAdapter::getDBConnection()) {
		$config = new \Doctrine\DBAL\Configuration();

		$connectionParams = [
		  'dbname'   => 'katanium',
		  'user'     => 'root',
		  'password' => '',
		  'host'     => 'localhost',
		  'driver'   => 'pdo_mysql',
		];

		DatabaseAdapter::setConnection(\Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config));
	}

	return $next($req, $res);
});

// $app->add(new \Katanium\Middlewares\CheckRememberMe($app));
