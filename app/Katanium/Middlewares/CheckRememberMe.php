<?php

namespace Katanium\Middlewares;

use Slim\Http\Cookies;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
* Handles the Remember Me Cookie
*
* This class generally check "Remember Me" procedure, whether it's ticked or not
*/
class CheckRememberMe
{
	protected $app;
	protected $c;

	public function __construct($app)
	{
		$this->app = $app;
		$this->c   = $app->getContainer();
	}

	/**
	 * Method that's called first
	 *
	 * @param  ServerRequestInterface $req  PSR-7 Request
	 * @param  ResponseInterface      $res  PSR-7 Response
	 * @param  callable               $next Call the next middleware
	 *
	 * @return callable
	 */
	public function __invoke(Request $req, Response $res, callable $next)
	{
		if (isset( $_SESSION[ $this->c->get('myConfig')['auth']['session'] ] )) { // Check wether user is authenticated or not
			$this->app->auth = User::find( $_SESSION[ $this->c->get('myConfig')['auth']['session'] ] );
		}

		$this->checkRememberMe($req, $res);
		$this->c->view->offsetSet('auth', $this->app->auth); // Give tha auth state to the whole app!

		return $next($request, $response);
	}

	/**
	 * Check Remember Me
	 *
	 * This method checks whether RememberMe cookie exists
	 * If it does, get the user credentials and make it global for easy use
	 * If not, just route the login page
	 *
	 * @param  ServerRequestInterface $req  PSR-7 Request
	 * @param  ResponseInterface      $res  PSR-7 Response
	 *
	 * @return callable
	 */
	protected function checkRememberMe(Request $req, Response $res)
	{
		dd(Cookies::get('hihi')); die();
		$cookie = $this->app->cookies->get($this->c->get('myConfig')['auth']['remember']);

		if ($cookie && ! $this->app->auth) {
			$credentials = explode('___', $cookie);

			// If the cookie isn't valid
			if (empty(trim($cookie)) || count($credentials) !== 2) {
				return $res->withHeader('Location', $this->app->router->pathFor('login'));
			}

			$identifier = $credentials[0];
			$hashLib    = $this->c->get('hash');
			$token      = $hashLib->hash($credentials[1]);

			$user = User::where('remember_identifier', $identifier)->first();

			if ($user) {
				if ($hashLib->hashCheck($token, $user->remember_token)) {
					// Finally, user can login
					$_SESSION[ $this->c->get('myConfig')['auth']['session'] ] = $user->user_id;
					$this->app->auth = $user;
				}
				else {
					$user->removeRememberCredentials();
				}
			} // Endif user is found in DB

		} // Endif the cookie is there
	}
}
