<?php

use Katanium\Models\Tag;

$app->group('/tag', function() use($app) {

	// Get the tags!
	$app->get('/trending', function() use($app) {
		// render it!
		$app->response->headers->set('Content-Type', 'application/json');
		echo json_encode(Tag::all());
	})->name('tag.trending');

	// Create the post!
	$app->post('/', function() use($app) {
		$tag = $app->request->post();

		print_r($tag);
	});
});