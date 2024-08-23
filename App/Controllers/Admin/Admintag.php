<?php

namespace App\Controllers\Admin;

use App\Libs\Controller;
use App\Models\TagsModel;

/*
* Tags Controller
*/
class Admintag extends Controller
{
  public $current_user;

  // Get Current User
  public function __construct()
  {
      $this->current_user = $this->currentuser(2);
      if ($this->current_user->class < CONFIG['_MODERATOR']) {
          showMessage(CONFIG['URLROOT'], LANG["NO_PERMISSION"]);
      }
  }

  // Tag Default Page
  public function index()
  {
    if (!empty($_POST)) {
      (new TagsModel)->insertOrUpdate();
    } else {
      // Get Tag Data
      $tags = (new TagsModel)->getAll();

      // Data For View
      $data = [
        'page_title' => LANG['TAGS'],
        'V_tags' => $tags
      ];

      // Load View
      $this->view('tag', $data, 'admin');
    }
  }

  public function delete()
  {
    (new TagsModel)->delete();
  }
}