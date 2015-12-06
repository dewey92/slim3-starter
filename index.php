<?php

ini_set('session.save_path', getcwd(). '/tmp');
session_cache_limiter(false);
session_start();
ini_set('display_error', 'On');

function dd($data) {
  echo "<pre>"; var_dump($data); die('</pre>');
}

require 'vendor/autoload.php';

require 'app/containers.php'; // Set containers, with variable $c

$app = new \Slim\App($c); // Create the app

require 'app/middlewares.php'; // Register middlewares

$app->auth = false; // Set login state

// Homepage
$app->get('/', function ($req, $res, $args = []) {

  $nameKey  = $this->csrf->getTokenNameKey();
  $valueKey = $this->csrf->getTokenValueKey();

  $data = [
    'name_key'  => $nameKey,
    'value_key' => $valueKey,
    'name'      => $req->getAttribute($nameKey),
    'value'     => $req->getAttribute($valueKey),
    'home'      => true
  ];

  return $this->view->render($res, 'home.twig' , $data);
})->setName('home');

// Load the filter
//require 'app/filters.php';

/*
// All routes goes here
require 'app/routes/auth/login.php';
require 'app/routes/auth/register.php';
require 'app/routes/auth/activate.php';
require 'app/routes/auth/logout.php';

require 'app/routes/dashboard.php';

require 'app/routes/user.php';
require 'app/routes/write.php';
require 'app/routes/post.php';
require 'app/routes/tag.php';
require 'app/routes/email.php';

require 'app/routes/errors/404.php';*/

$app->run();
