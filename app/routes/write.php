<?php

use Katanium\Models\Post;

$app->get('/write', $registered(), function() use($app) {

	$session['key'] = $app->config->get('auth.session');
	$session['value'] = $_SESSION[ $app->config->get('auth.session') ];

	$app->view()->appendData([ 'write' => true, 'session' => $session ]);
	$app->render('write.twig');

})->name('write');

// Create the post!
$app->post('/write', $registered(), function() use($app) {

	// Add custom functions!
	require_once 'app/functions/slugify.php';

	$req     = $app->req;
	$user_id = $_SESSION[$app->config->get('auth.session')];

	$post = new Post();
	$post->title        = $req->title;
	$post->content      = $req->content;
	$post->content_text = $req->content_text;
	$post->author       = $user_id;
	$post->slug         = slugify($req->title);
	$post->status       = 'published';

	if (isset($_FILES['file'])) {
		// Upload photo first
		$storage = new \Upload\Storage\FileSystem($_SERVER['DOCUMENT_ROOT'] . '/user-uploads/' . $user_id);
		$file = new \Upload\File('file', $storage);

		// Optionally you can rename the file on upload
		$new_filename = uniqid();
		$file->setName($new_filename);

		// Validate file upload
		// MimeType List => http://www.iana.org/assignments/media-types/media-types.xhtml
		$file->addValidations(array(
				// Ensure file is of type "image/png"
				new \Upload\Validation\Mimetype(['image/png', 'image/jpg', 'image/jpeg', 'image/gif', 'image/svg']),

				// Ensure file is no larger than 5M (use "B", "K", M", or "G")
				new \Upload\Validation\Size('2M')
		));

		// Access data about the file that has been uploaded
		$data = array(
				'name' => $file->getNameWithExtension(),
				// 'extension'  => $file->getExtension(),
				// 'mime'       => $file->getMimetype(),
				// 'size'       => $file->getSize(),
				// 'md5'        => $file->getMd5(),
				// 'dimensions' => $file->getDimensions()
		);

		$post->post_image = $data['name'];

		// Try to upload file
		try { // Success!
			$file->upload();

			$post->save();
		} catch (\Exception $e) { // Fail!
			$errors = $file->getErrors();
		}
	}
	else {
		$post->save();
	}

})->name('write.post');
