<?php

namespace App\Controllers\Admin;

use App\Libs\User;
use App\Models\TagsModel;

/*
* Tags Controller
*/
class Admintag
{
  // Tag Default Page
  public function index()
  {
    $USER = new User(_MODERATOR, 2);
    if (!empty($_POST)) {
      TagsModel::insertOrUpdate();
    } else {
      // Get Tag Data
      $tags = TagsModel::getAll();

      // Init Data
      $data = [
        'V_title' => Txt('TAGS'),
        'V_tags' => $tags,
        'USER' => $USER
      ];

      // Load View
      template('tag', $data, 'admin');
    }
  }

  public function delete()
  {
    $USER = new User(_MODERATOR, 2);
    TagsModel::delete();
  }
}