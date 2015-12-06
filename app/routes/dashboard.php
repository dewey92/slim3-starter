<?php

use Katanium\Models\Post;

$app->get('/dashboard(/:whatever)', $registered(), function ($whatever = 'summary') use($app) {
	$app->view()->appendData(['dashboard' => true]);
	$app->render('dashboard/index.twig');
})->name('dashboard');

$app->group('/myposts', function() use($app, $registered) {

	/**
	 * Get the logged-in user posts
	 */
	$app->get('/', $registered(), function () use($app) {
		// Get the logged-in user posts
		$posts = (new Post())->searchPost('', $app->auth->user_id);

		echo json_encode($posts->withTrashed()->get());
	});

	/**
	 * Filter the posts according to the status
	 */
	$app->get('/status/:status', $registered(), function ($status) use($app) {
		// Get the logged-in user posts
		$posts = Post::where('author', $app->auth->user_id)->ofStatus($status);

		echo json_encode($posts->withTrashed()->get());
	});

	/**
	 * Mutate the status of the post 
	 */
	$app->put('/:postID/make/:action', $registered(), function ($postID, $action) use($app) {
		// Get the logged-in user posts
		$post      = Post::find($postID);
		$condition = ! post || ($post->author !== $app->auth->user_id);

		switch ($action) {
			case 'published':
				if ($condition || ($post->status !== 'draft')) return;
				$post->status = $action;
				break;

			case 'draft':
				if ($condition || ($post->status !== 'published')) return;
				$post->status = $action;
				break;

			case 'deleted':
				if ($condition || ($post->status === 'deleted')) return;
				$post->status = $action;
				break;

			case 'restored':
				if ($condition || ($post->status !== 'deleted')) return;
				$post->status = 'published';
				break;
			
			default:
				return $app->notFound();
				break;
		}
	});

});