<?php

use \RandomLib\Factory as RandomLib;
use Katanium\Helpers\Hash;
use Katanium\Helpers\Validator;
use Katanium\Mail\Mailer;

require 'config/development.php';
require 'config/production.php';

$c = new \Slim\Container();

/**
 * Simply returns app-mode
 *
 * @return srtring
 */
$c['mode'] = function ($c) {
  return 'development';
};

/**
 * Get configurations
 *
 * the functions inside refer to the two files
 * included at the top of the file
 *
 * @return array
 */
$c['myConfig'] = function ($c) {
  return $c->get('mode') === 'development' ? getDevelopmentSettings() : getProductionSettings();
};

/**
 * Get Slim Setting
 *
 * @return array
 */
$c['settings'] = function ($c) {
  return [
    'httpVersion'                       => '1.1',
    'responseChunkSize'                 => 4096,
    'outputBuffering'                   => 'append',
    'determineRouteBeforeAppMiddleware' => false,
    'displayErrorDetails'               => $c->get('mode') === 'development' ? true : false
  ];
};

/**
 * Register CSRF Guard
 *
 * @return \Guard
 */
$c['csrf'] = function ($c) {
  return new \Slim\Csrf\Guard;
};

/**
 * Configure how the app reacts to 500
 *
 * @return Closure
 */
$c['errorHandler'] = function ($c) {
  return function ($request, $response, $exception) use ($c) {
    /*return $c['response']->withStatus(500)
      ->withHeader('Content-Type', 'text/html')
      ->write('Something went wrong!');*/
  };
};

/**
 * Register Twig View Helper with some view configs
 *
 * @return \Twig
 */
$c['view'] = function ($c) {
  $view = new \Slim\Views\Twig( __DIR__ . '/views', [
    'cache' => false // "../path/to/cache"
  ]);

  // Instantiate and add Slim specific extension
  $view->addExtension(new \Slim\Views\TwigExtension(
    $c['router'],
    $c['request']->getUri()
  ));

  $view->addExtension(new \Katanium\Extensions\MyTwigExtension);

  $twig = $view->getEnvironment(); // Configure view lexer
  $lexer = new Twig_Lexer($twig, array(
    'tag_comment'   => array('{#', '#}'),
    'tag_block'     => array('{%', '%}'),
    'tag_variable'  => array('${', '}'),
    'interpolation' => array('#{', '}')
  ));
  $twig->setLexer($lexer);

  return $view;
};

/**
 * Register Hash library
 *
 * @return \Hash
 */
$c['hash'] = function ($c) {
  return new Hash($c->get('myConfig'));
};

/**
 * Register RandomLib library
 *
 * @return \RandomLib
 */
$c['randomlib'] = function ($c) {
  $factory = new RandomLib;
  return $factory->getMediumStrengthGenerator();
};

/**
 * Register Validator library
 *
 * @return \Validator
 */
$c['validator'] = function ($c) {
  return new Validator;
};

/**
 * Register PHPMailer library
 *
 * @return \PHPMailer
 */
$c['mail'] = function ($c) {
  $mailer = new PHPMailer;

  $mailer->isSMTP();
  $mailer->isHTML( $app->config->get('mail.html') );
  $mailer->Host       = $app->config->get('mail.host');
  $mailer->SMTPAuth   = $app->config->get('mail.smtp_auth');
  $mailer->SMTPSecure = $app->config->get('mail.smtp_secure');
  $mailer->Port       = $app->config->get('mail.port');
  $mailer->Username   = $app->config->get('mail.username');
  $mailer->Password   = $app->config->get('mail.password');

  $mailer->FromName = 'Katanium';

  if($c->get('mode') === 'production') {
    $mailer->SMTPOptions = [
      'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      ]
    ];
  }

  return new Mailer( $app->view, $mailer );
};
