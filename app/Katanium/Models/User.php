<?php

namespace Katanium\Models;

use Katanium\Helpers\DatabaseAdapter;

class User extends DatabaseAdapter {

  /**
   * Get user posts
   *
   * @param  int $user_id User ID
   *
   * @return array
   */
  public function posts($user_id)
  {
    $stmt = $this->db->prepare("
      SELECT p.*
      FROM user u
      JOIN post s ON p.author_id = u.id
      WHERE u.id = :user_id
    ");
    $stmt->bindValue('user_id', $user_id);
    $stmt->execute();

    return $stmt->fetchAll();
  }

  /**
     * Scope a query to only include active users.
     */
  public function scopeActive($query)
  {
    return $query->where('active', 1);
  }

  public function activateAccount()
  {
    $this->active      = 1;
    $this->active_hash = null;

    $this->save();
  }

  public function getAvatarUrl( $options = array() ) {
    $size = isset($options['size']) ? $options['size'] : 45;

    return 'http://www.gravatar.com/avatar/' . md5( $this->email ) . '?s=' . $size . '&d=identicon';
  }

  public function updateRememberCredentials( $identifier, $token )
  {
    $this->remember_identifier = $identifier;
    $this->remember_token      = $token;

    $this->save();
  }

  public function removeRememberCredentials()
  {
    $this->updateRememberCredentials(null, null);
  }
}