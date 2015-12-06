<?php

use Katanium\Models\Post;
use Katanium\Models\Comment;

$app->group('/post', function () {

  /**
   * Get all posts
   *
   * used in homepage, get the post along with
   * author, number of likes, and number of comments
   *
   * @return void
   */
  $app->get('/all', function ($req, $res, $args = []) {
    $result = Post::getAll();

    return $res->withHeader('Content-Type', 'application/json');
    echo json_encode($posts);
  })->setName('post.all');


  /**
   * GET POSTS TO READ
   * used in sidebar widget to get random posts to read
   *
   * @return void
   */
  $app->get('/to-read', function ($req, $res, $args = []) {
    $allPost = Post::get(['post_id', 'title', 'slug', 'author']);
    $posts   = array();

    foreach ($allPost as $post) {
      $post->user;
      $posts[] = $post;
    }

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($posts);
  })->setName('post.random');


  /**
   * GET SINGLE POST DETAIL
   * first check the slug, then display it with view + 1
   *
   * @param [int] post ID
   * @param [string] slug (optional)
   */
  $app->get('/{postID}[/{slug}]', function($req, $res, $args = []) {
    $post = Post::find( $postID );

    if (! $post) { // No post, display 404
      return $app->notFound();
    }

    /*if ($post->slug !== $slug) { // If the slug doesn't match with that in DB, re-route it with the correct URL for SEO purpose
      return $app->redirect( $app->urlFor('post.detail', ['postID' => $postID, 'slug' => $post->slug]) );
    }*/

    /*$post->views = $post->views + 1; // Update the view field
    $post->save();
    $post->user; //get the user info*/

    $data['post'] = $post;
    $this->view->render($response, 'post-detail.twig', $data);
  })->setName('post.detail');


  /**
   * GET COMMENTS
   * Get comments of a single post
   *
   * @param [int] post ID
   *
   * @return void
   */
  $app->get('/comments-for/:postID', function($req, $res, $args = []) {
    $post = Post::find($postID);

    if (! $post) { // No post, display 404
      return $app->notFound();
    }

    $comments = [];
    foreach ($post->comments as $comment) {
      $comment->userDisplayName = $comment->user->displayName;
      $comment->userFullName    = $comment->user->fullName;
      $comment->userAvatar      = '/user-uploads/' . $comment->user->user_id .'/'. $comment->user->avatar;
      unset($comment->user);

      $comments[] = $comment;
    }

    echo json_encode($comments);
  })->setName('post.comments');


  /**
   * SEARCH POST
   * used to search any posts
   *
   * @param [string] post query         : The search text inputted by user
   * @param [int]    user ID (optional) : used to find any posts authored by the ID
   *
   * @return void
   */
  $app->get('/search/:postQuery[/:userIdQuery]', function($req, $res, $args = []) {
    $postQuery = htmlspecialchars($postQuery, ENT_QUOTES, 'UTF-8');
    $post = (new Post())->searchPost(trim($postQuery), $userIdQuery);

    $app->response->headers->set('Content-Type', 'application/json');
    echo json_encode($post->get());
  })->setName('post.search');


  /**
   * EDIT POST
   * update post for registered users only
   *
   * @param [int] post ID
   */
  $app->put('/:postID/edit', $registered(), function($req, $res, $args = []) {
    $post = Post::find($postID);

    if (! $post) { // No post, display 404
      return $app->notFound();
    }

    if ($post->author !== $_SESSION[$app->config->get('auth.session')]) { // Check if the post is his own
      throw new \Exception('Oops! You don\'t have access to other\'s post');
    }

    require_once 'app/functions/slugify.php';

    $post->title   = $req->title;
    $post->slug    = slugify($req->title);
    $post->content = $req->content;
    $post->author  = $user_id;

    // Yang gambar beloman, nanti aja ya..
  })->setName('post.edit');


  /**
   * DELETE POST
   * delete a single post, can only be done by registered users only
   *
   * @param [int] post ID
   */
  $app->delete('/:postID/delete', $registered(), function($req, $res, $args = []) {
    $post = Post::find($postID);

    if (! $post) { // No post, display 404
      return $app->notFound();
    }

    if ($post->author !== $_SESSION[$app->config->get('auth.session')]) { // Check if the post is his own
      throw new \Exception('Oops! You don\'t have access to other\'s post');
    }

    $post->delete();
  })->setName('post.delete');


  /**
   * COMMENT
   * comment system, how the app handle comments inputted by other users
   *
   * @param post ID
   */
  $app->post('/:postID/comment', $registered(), function($req, $res, $args = []) {
    $req = $app->req;

    $v = $app->validator; // Set validator
    $v->addFieldMessage('comment', 'required', 'Komentar wajib diisi');
    $v->validate([
      'comment' => [ trim($req->comment), 'required' ]
    ]);

    if ($v->passes()) {
      $comment = new Comment();
      $comment->author  = $_SESSION[$app->config->get('auth.session')];
      $comment->post_id = $postID;
      $comment->content = $req->comment;
      $comment->save();
    }
    else {
      $errors = $v->errors()->all();

      $app->response->headers->set('Content-Type', 'application/json'); // Output the message
      $app->response->setStatus(503);
      echo json_encode([ 'msg' => $errors, 'error' => true ]);
    }
  })->setName('post.post.comment');

});
