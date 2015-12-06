<?php

use Katanium\Models\User;
use Katanium\Models\Post;

$app->group('/user', function() use($app) {

	/**
	 * USER PROFILE
	 * display the desired user profile
	 *
	 * @param [string] displayname : sort of username, not fullname
	 */
	$app->get('/:displayName', function( $displayName ) use($app) {
		$user = User::where('displayName', $displayName)->first();

		if (! $user) {
			$app->notFound();
		}

		$app->view()->appendData(['user' => $user]);
		$app->render('user-profile.twig');
	})->name('user');


	/**
	 * USER POSTS
	 * get all posts authored by the user
	 *
	 * @param [string] displayname     : user's username
	 * @param [int]    page (optional) : number used to paginate the posts
	 * @return all paginated user posts in JSON
	 */
	$app->get('/:displayName/posts(/:page)', function($displayName, $page = 0) use($app) {
		$user = User::where('displayName', $displayName)->first(['user_id']);

		if (! $user) {
			$app->notFound();
		}

		$userPosts = (new Post())->searchPost('', $user->user_id);
		$userPosts->where('status', 'published');

		$app->response->headers->set('Content-Type', 'application/json');
		echo json_encode($userPosts->get());
	})->name('user.posts');

});
