<?php

namespace Katanium\Models;

use Katanium\Helpers\DatabaseAdapter;

class Post extends DatabaseAdapter {


	/**
	 * Get a single post
	 *
	 * Return the post detail with the author information
	 * based on the given ID
	 *
	 * @param int $post_id The id of the post
	 *
	 * @return array
	 */
	public function getPost($post_id)
	{
		$stmt = $this->db->prepare("
			SELECT
				p.id, p.title, p.content, p.created_at, p.updated_at, p.url, p.post_image, p.status, p.views, p.author_id,
				u.displayname, u.fullname, u.avatar
			FROM post p
			JOIN user u ON u.id = p.author_id
			WHERE p.id = :post_id
			LIMIT 1
		");
		$stmt->bindValue('post_id', $post_id);
		$stmt->execute();

		return $stmt->fetch();
	}

	public function getAll($limit = 20, $offset = 0)
	{
		$stmt = $this->db->prepare("
			SELECT
				p.id, p.title, p.content, p.created_at, p.updated_at, p.url, p.post_image, p.status, p.views, p.author_id,
				u.displayname, u.fullname, u.avatar
			FROM post p
			JOIN user u ON u.id = p.author_id
			ORDER BY p.created_at DESC
			LIMIT :limit OFFSET :offset
		");
		$stmt->bindValue('limit', $limit);
		$stmt->bindValue('offset', $offset);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/**
	 * Get post comments
	 *
	 * @param int $post_id The id of the post
	 *
	 * @return array
	 */
	public function comments($post_id)
	{
		$stmt = $this->db->prepare("
			SELECT
				c.id, c.created_at, c.updated_at, c.content,
				c.author_id, u.displayname
			FROM comment c
			JOIN post p ON p.id = c.post_id
			JOIN user u ON u.id = c.author_id
			WHERE p.id = :post_id
		");
		$stmt->bindValue('post_id', $post_id);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/*public function searchPost($postQuery, $userIdQuery = null) {
		$post = $this->query();

		$post->orderBy('created_at', 'desc');

		if ($userIdQuery) {
			$post->where('author', $userIdQuery);
		}

		if ($postQuery == '') {
			return $post;
		}
		else {
			return $post->where('title', 'like', '%' .$postQuery. '%')->orWhere('content_text', 'like', '%' .$postQuery. '%');
		}
	}

	public function scopeOfStatus($query, $status)
	{
		return $query->where('status', $status)->orderBy('created_at', 'desc');
	}*/

}